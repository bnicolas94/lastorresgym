<?php
// Si el servidor soporta .htaccess (Apache), esto será ignorado por la regla de reescritura.
// Si no (Nginx sin config), esto redirigirá al usuario a la carpeta pública.
header("Location: public/");
exit;
?>