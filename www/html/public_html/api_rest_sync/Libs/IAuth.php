<?php
namespace Libs;
interface IAuth {
    public function autenticar($datos);
    public function estaAutenticado();
    public function destruir();
    public function user();
}
