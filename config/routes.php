<?php
// config/routes.php - Enhanced Routing Configuration

class Router {
    private $routes = [];
    private $middlewares = [];
    
    public function __construct() {
        $this->loadRoutes();
    }
    
    private function loadRoutes() {
        // Settings Routes with Role-Based Access Control
        $this->addSettingsRoutes();
        
        // API Routes
        $this->addApiRoutes();
        
        // Work Shifts Routes
        $this->addWorkShiftRoutes();
    }
    
    private function addSettingsRoutes() {
        // Main settings page - requires at least manager role
        $this->add('GET', '/settings', 'SettingsController@index', ['role:manager|hr|admin']);
        
        // Update settings - different permissions for different actions
        $this->add('POST', '/settings/update', 'SettingsController@update', ['role:manager|hr|admin']);
        
        // Theme preview - requires HR or Admin
        $this->add('POST', '/settings/preview-theme', 'SettingsController@previewTheme', ['role:hr|admin']);
        
        // Export/Import - Admin only
        $this->add('GET', '/settings/export', 'SettingsController@export', ['role:admin']);
        $this->add('POST', '/settings/import', 'SettingsController@import', ['role:admin']);
        $this->add('POST', '/settings/reset', 'SettingsController@reset', ['role:admin']);
        
        // Individual setting management
        $this->add('GET', '/settings/get/{key}', 'SettingsController@getSetting', ['role:manager|hr|admin']);
        $this->add('POST', '/settings/update/{key}', 'SettingsController@updateSetting', ['role:manager|hr|admin']);
    }
    
    private function addWorkShiftRoutes() {
        // Work shift management - requires manager role or higher
        $this->add('POST', '/settings/createShift', 'SettingsController@createShift', ['role:manager|hr|admin']);
        $this->add('POST', '/settings/updateShift', 'SettingsController@updateShift', ['role:manager|hr|admin']);
        $this->add('POST', '/settings/deleteShift', 'SettingsController@deleteShift', ['role:hr|admin']);
        
        // Shift assignments
        $this->add('POST', '/settings/assignShift', 'SettingsController@assignShift', ['role:manager|hr|admin']);
        $this->add('POST', '/settings/removeEmployeeFromShift', 'SettingsController@removeEmployeeFromShift', ['role:manager|hr|admin']);
        
        // Shift data retrieval
        $this->add('GET', '/settings/getShift', 'SettingsController@getShift', ['role:manager|hr|admin']);
        $this->add('GET', '/settings/getShiftEmployees', 'SettingsController@getShiftEmployees', ['role:manager|hr|admin']);
        $this->add('GET', '/settings/getShiftSchedule', 'SettingsController@getShiftSchedule', ['role:manager|hr|admin']);
    }
    
    private function addApiRoutes() {
        // REST API routes
        $this->add('GET', '/api/settings', 'SettingsAPI@getAllSettings', ['role:manager|hr|admin']);
        $this->add('GET', '/api/settings/{key}', 'SettingsAPI@getSetting', ['role:manager|hr|admin']);
        $this->add('POST', '/api/settings', 'SettingsAPI@updateSettings', ['role:manager|hr|admin']);
        $this->add('PUT', '/api/settings/{key}', 'SettingsAPI@updateSingleSetting', ['role:manager|hr|admin']);
        $this->add('DELETE', '/api/settings/{key}', 'SettingsAPI@deleteSetting', ['role:admin']);
        
        // Shift API routes
        $this->add('GET', '/api/settings/shifts', 'SettingsAPI@getAllShifts', ['role:manager|hr|admin']);
        $this->add('GET', '/api/settings/shifts/{id}', 'SettingsAPI@getShift', ['role:manager|hr|admin']);
        $this->add('POST', '/api/settings/shifts', 'SettingsAPI@createShift', ['role:manager|hr|admin']);
        $this->add('PUT', '/api/settings/shifts/{id}', 'SettingsAPI@updateShift', ['role:manager|hr|admin']);
        $this->add('DELETE', '/api/settings/shifts/{id}', 'SettingsAPI@deleteShift', ['role:hr|admin']);
        
        // Validation and utility APIs
        $this->add('POST', '/api/settings/validate', 'SettingsAPI@validateSettings', ['role:manager|hr|admin']);
        $this->add('GET', '/api/settings/permissions', 'SettingsAPI@getUserPermissions', ['auth']);
        $this->add('POST', '/api/settings/backup', 'SettingsAPI@createBackup', ['role:hr|admin']);
        $this->add('POST', '/api/settings/restore', 'SettingsAPI@restoreBackup', ['role:admin']);
    }
    
    public function add($method, $path, $handler, $middleware = []) {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'handler' => $handler,
            'middleware' => $middleware
        ];
    }
    
    public function dispatch($method, $uri) {
        $method = strtoupper($method);
        $uri = parse_url($uri, PHP_URL_PATH);
        
        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }
            
            $pattern = $this->convertToRegex($route['path']);
            if (preg_match($pattern, $uri, $matches)) {
                // Extract parameters
                $params = array_slice($matches, 1);
                
                // Check middleware
                if (!$this->checkMiddleware($route['middleware'])) {
                    $this->handleUnauthorized();
                    return;
                }
                
                // Execute handler
                $this->executeHandler($route['handler'], $params);
                return;
            }
        }
        
        // Route not found
        $this->handleNotFound();
    }
    
    private function convertToRegex($path) {
        // Convert route parameters like {id} to regex groups
        $pattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $path);
        return '#^' . $pattern . '$#';
    }
    
    private function checkMiddleware($middlewares) {
        foreach ($middlewares as $middleware) {
            if (!$this->executeMiddleware($middleware)) {
                return false;
            }
        }
        return true;
    }
    
    private function executeMiddleware($middleware) {
        if (strpos($middleware, 'role:') === 0) {
            return $this->checkRoleMiddleware(substr($middleware, 5));
        }
        
        switch ($middleware) {
            case 'auth':
                return $this->checkAuthentication();
            case 'admin':
                return RoleHelper::isAdmin();
            case 'hr':
                return RoleHelper::isHR();
            case 'manager':
                return RoleHelper::isManager();
            default:
                return true;
        }
    }
    
    private function checkRoleMiddleware($roles) {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            return false;
        }
        
        $allowedRoles = explode('|', $roles);
        $userPermissions = RoleHelper::getUserPermissions();
        
        foreach ($allowedRoles as $role) {
            switch (trim($role)) {
                case 'admin':
                    if ($userPermissions['can_manage_settings']) return true;
                    break;
                case 'hr':
                    if ($userPermissions['can_manage_employees']) return true;
                    break;
                case 'manager':
                    if ($userPermissions['can_view_reports']) return true;
                    break;
            }
        }
        
        return false;
    }
    
    private function checkAuthentication() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['user_id']);
    }
    
    private function executeHandler($handler, $params) {
        if (strpos($handler, '@') !== false) {
            list($controller, $method) = explode('@', $handler);
            
            $controllerFile = __DIR__ . "/../controllers/{$controller}.php";
            if (file_exists($controllerFile)) {
                require_once $controllerFile;
                
                if (class_exists($controller)) {
                    $instance = new $controller();
                    if (method_exists($instance, $method)) {
                        call_user_func_array([$instance, $method], $params);
                        return;
                    }
                }
            }
        } elseif (is_callable($handler)) {
            call_user_func_array($handler, $params);
            return;
        }
        
        $this->handleNotFound();
    }
    
    private function handleNotFound() {
        http_response_code(404);
        if ($this->isApiRequest()) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => 'Route not found',
                'code' => 404
            ]);
        } else {
            require_once __DIR__ . '/../views/errors/404.php';
        }
    }
    
    private function handleUnauthorized() {
        http_response_code(403);
        if ($this->isApiRequest()) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => 'Insufficient permissions',
                'code' => 403
            ]);
        } else {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['error_message'] = 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้';
            header('Location: ' . BASE_URL . '/dashboard');
        }
    }
    
    private function isApiRequest() {
        return strpos($_SERVER['REQUEST_URI'], '/api/') !== false ||
               (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);
    }
    
    public function getRoutes() {
        return $this->routes;
    }
}

// index.php integration
/*
// Add this to your main index.php file:

require_once 'config/routes.php';
require_once 'helpers/RoleHelper.php';

$router = new Router();

// Get current method and URI
$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Remove query string
$uri = strtok($uri, '?');

// Remove base URL if present
if (defined('BASE_URL')) {
    $basePath = parse_url(BASE_URL, PHP_URL_PATH);
    if ($basePath && strpos($uri, $basePath) === 0) {
        $uri = substr($uri, strlen($basePath));
    }
}

// Dispatch the request
$router->dispatch($method, $uri);
*/

// Advanced Routing Features
class AdvancedRouter extends Router {
    private $groups = [];
    private $currentGroup = '';
    
    public function group($prefix, $callback, $middleware = []) {
        $previousGroup = $this->currentGroup;
        $this->currentGroup = rtrim($prefix, '/');
        
        $this->groups[$this->currentGroup] = $middleware;
        
        call_user_func($callback, $this);
        
        $this->currentGroup = $previousGroup;
    }
    
    public function add($method, $path, $handler, $middleware = []) {
        $fullPath = $this->currentGroup . $path;
        $groupMiddleware = isset($this->groups[$this->currentGroup]) ? $this->groups[$this->currentGroup] : [];
        $allMiddleware = array_merge($groupMiddleware, $middleware);
        
        parent::add($method, $fullPath, $handler, $allMiddleware);
    }
    
    // Resource routing for CRUD operations
    public function resource($name, $controller, $middleware = []) {
        $this->add('GET', "/{$name}", "{$controller}@index", $middleware);
        $this->add('GET', "/{$name}/create", "{$controller}@create", $middleware);
        $this->add('POST', "/{$name}", "{$controller}@store", $middleware);
        $this->add('GET', "/{$name}/{id}", "{$controller}@show", $middleware);
        $this->add('GET', "/{$name}/{id}/edit", "{$controller}@edit", $middleware);
        $this->add('PUT', "/{$name}/{id}", "{$controller}@update", $middleware);
        $this->add('DELETE', "/{$name}/{id}", "{$controller}@destroy", $middleware);
    }
    
    // API resource routing
    public function apiResource($name, $controller, $middleware = []) {
        $this->add('GET', "/api/{$name}", "{$controller}@index", $middleware);
        $this->add('POST', "/api/{$name}", "{$controller}@store", $middleware);
        $this->add('GET', "/api/{$name}/{id}", "{$controller}@show", $middleware);
        $this->add('PUT', "/api/{$name}/{id}", "{$controller}@update", $middleware);
        $this->add('DELETE', "/api/{$name}/{id}", "{$controller}@destroy", $middleware);
    }
}

// Settings-specific routing setup
class SettingsRouter extends AdvancedRouter {
    
    public function setupSettingsRoutes() {
        // Admin-only routes
        $this->group('/settings/admin', function($router) {
            $router->add('GET', '/system', 'SettingsController@systemSettings');
            $router->add('GET', '/security', 'SettingsController@securitySettings');
            $router->add('POST', '/reset-all', 'SettingsController@resetAll');
            $router->add('GET', '/logs', 'SettingsController@viewLogs');
            $router->add('GET', '/backups', 'SettingsController@listBackups');
            $router->add('DELETE', '/backups/{id}', 'SettingsController@deleteBackup');
        }, ['role:admin']);
        
        // HR routes
        $this->group('/settings/hr', function($router) {
            $router->add('GET', '/organization', 'SettingsController@organizationSettings');
            $router->add('GET', '/theme', 'SettingsController@themeSettings');
            $router->add('GET', '/notifications', 'SettingsController@notificationSettings');
        }, ['role:hr|admin']);
        
        // Manager routes
        $this->group('/settings/manager', function($router) {
            $router->add('GET', '/worktime', 'SettingsController@worktimeSettings');
            $router->add('GET', '/shifts', 'SettingsController@shiftSettings');
        }, ['role:manager|hr|admin']);
        
        // API routes with versioning
        $this->group('/api/v1/settings', function($router) {
            $router->apiResource('settings', 'SettingsAPI');
            $router->apiResource('shifts', 'WorkShiftAPI');
            
            // Custom API endpoints
            $router->add('POST', '/validate-bulk', 'SettingsAPI@validateBulk');
            $router->add('GET', '/schema', 'SettingsAPI@getSchema');
            $router->add('POST', '/migrate', 'SettingsAPI@migrate');
        }, ['role:manager|hr|admin']);
    }
}

// Route caching for better performance
class CachedRouter extends SettingsRouter {
    private $cacheFile;
    private $cacheTime = 3600; // 1 hour
    
    public function __construct() {
        $this->cacheFile = __DIR__ . '/../cache/routes.cache';
        parent::__construct();
    }
    
    public function dispatch($method, $uri) {
        // Try to load from cache first
        if ($this->loadFromCache()) {
            return parent::dispatch($method, $uri);
        }
        
        // Load routes normally
        $this->loadRoutes();
        $this->setupSettingsRoutes();
        
        // Cache the routes
        $this->saveToCache();
        
        return parent::dispatch($method, $uri);
    }
    
    private function loadFromCache() {
        if (!file_exists($this->cacheFile)) {
            return false;
        }
        
        if (time() - filemtime($this->cacheFile) > $this->cacheTime) {
            return false;
        }
        
        $cached = file_get_contents($this->cacheFile);
        $data = unserialize($cached);
        
        if ($data && isset($data['routes'])) {
            $this->routes = $data['routes'];
            $this->groups = $data['groups'];
            return true;
        }
        
        return false;
    }
    
    private function saveToCache() {
        $cacheDir = dirname($this->cacheFile);
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }
        
        $data = [
            'routes' => $this->routes,
            'groups' => $this->groups,
            'timestamp' => time()
        ];
        
        file_put_contents($this->cacheFile, serialize($data));
    }
    
    public function clearCache() {
        if (file_exists($this->cacheFile)) {
            unlink($this->cacheFile);
        }
    }
}

// Usage example in main application
/*
// In your main routing file or bootstrap:

$router = new CachedRouter();
$router->setupSettingsRoutes();

// Custom middleware example
$router->add('GET', '/settings/debug', function() {
    if (!DEBUG_MODE) {
        http_response_code(404);
        exit;
    }
    
    require_once 'views/settings/debug.php';
}, ['role:admin']);

// Dispatch the request
$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
*/
?>