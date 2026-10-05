<?php
/**
 * Auth - Helpers de autenticacion y autorizacion
 *
 * Roles:
 *   1 = Administrador
 *   2 = Docente
 *   3 = Estudiante
 */
class Auth
{
    const ROL_ADMIN     = 1;
    const ROL_DOCENTE   = 2;
    const ROL_ESTUDIANTE = 3;

    public static function check()
    {
        return isset($_SESSION['usuario']);
    }

    public static function user()
    {
        return $_SESSION['usuario'] ?? null;
    }

    public static function id()
    {
        return $_SESSION['usuario']['id'] ?? null;
    }

    public static function rol()
    {
        return isset($_SESSION['usuario']['rol']) ? (int)$_SESSION['usuario']['rol'] : 0;
    }

    public static function isAdmin()
    {
        return self::rol() === self::ROL_ADMIN;
    }

    public static function isDocente()
    {
        return self::rol() === self::ROL_DOCENTE;
    }

    public static function isEstudiante()
    {
        return self::rol() === self::ROL_ESTUDIANTE;
    }

    public static function hasRole($roles)
    {
        if (is_array($roles)) {
            return in_array(self::rol(), $roles, true);
        }
        return self::rol() === (int)$roles;
    }

    public static function nombre()
    {
        return $_SESSION['usuario']['nombre'] ?? 'Usuario';
    }

    public static function rolLabel($rol = null)
    {
        $rol = $rol ?? self::rol();
        switch ((int)$rol) {
            case self::ROL_ADMIN:      return 'Administrador';
            case self::ROL_DOCENTE:    return 'Docente';
            case self::ROL_ESTUDIANTE: return 'Estudiante';
            default:                   return 'Desconocido';
        }
    }
}
