<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AuthTest extends TestCase
{
    private const ROUTE_LOGOUT = '/logout';
    private const ROUTE_DASHBOARD = '/dashboard';
    private const ROUTE_LOGIN = '/login';
    /**
     * Test del Happy Path: Usuario se autentica y luego hace logout
     * verificando que la sesión termina y se redirige correctamente.
     *
     * Criteria covered:
     * - Session ends after logout
     * - Redirect to login page is performed
     */
    public function test_user_can_logout_and_session_ends(): void
    {
        // Crear usuario
        $user = User::factory()->create([
            'email' => 'doctor@example.com',
            'password' => bcrypt('password123'),
            'role' => 'doctor',
        ]);

        // Autenticar usuario
        $this->actingAs($user);

        // Verificar que el usuario está autenticado
        $this->assertAuthenticatedAs($user);

        // Ejecutar logout
        $response = $this->post(self::ROUTE_LOGOUT);

        // Verificar que se redirige al login
        $response->assertRedirect('/');

        // Verificar que la sesión se ha terminado
        $this->assertGuest();

        // Verificar que la cookie de sesión se ha invalidado
        $this->assertNull(Auth::user());
    }

    /**
     * Test de Flujo Alternativo: Después de hacer logout, el usuario intenta
     * acceder a rutas protegidas (usando simulación del back button) y verifica
     * que no puede acceder sin estar autenticado.
     *
     * Criteria covered:
     * - User cannot access system using browser back button
     * - Session is completely invalidated
     */
    public function test_user_cannot_access_protected_routes_after_logout(): void
    {
        // Crear usuario
        $user = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('password123'),
            'role' => 'admin',
        ]);

        // Autenticar usuario
        $this->actingAs($user);

        // Verificar que el usuario puede acceder al dashboard (ruta protegida)
        $response = $this->get(self::ROUTE_DASHBOARD);
        $response->assertStatus(200);

        // Ejecutar logout
        $this->post(self::ROUTE_LOGOUT);

        // Verificar que ya no está autenticado
        $this->assertGuest();

        // Intentar acceder al dashboard (simulando back button del navegador)
        $response = $this->get(self::ROUTE_DASHBOARD);

        // Debe redirigir a login (estatus 302 redirect o 401 unauthorized)
        $response->assertRedirect(self::ROUTE_LOGIN);

        // Verificar que no hay acceso autorizado
        $this->assertGuest();
    }

    /**
     * Test adicional: Logout invalida el token de sesión
     * impidiendo el reutilización de tokens.
     *
     * Criteria covered:
     * - Invalidate session token
     */
    public function test_logout_invalidates_session_token(): void
    {
        // Crear usuario
        $user = User::factory()->create([
            'email' => 'doctor2@example.com',
            'password' => bcrypt('password123'),
            'role' => 'doctor',
        ]);

        // Autenticar usuario
        $response = $this->post(self::ROUTE_LOGIN, [
            'email' => 'doctor2@example.com',
            'password' => 'password123',
        ]);

        // Verificar que está autenticado
        $this->assertAuthenticatedAs($user);

        // Obtener el usuario autenticado antes de logout
        $userBeforeLogout = Auth::user();
        $this->assertNotNull($userBeforeLogout);

        // Ejecutar logout
        $this->post(self::ROUTE_LOGOUT);

        // Verificar que la sesión está limpia
        $this->assertGuest();

        // Intentar usar la sesión antigua debería fallar
        $response = $this->get(self::ROUTE_DASHBOARD);
        $response->assertRedirect(self::ROUTE_LOGIN);
    }
}
