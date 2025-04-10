<?php

return [
    App\Providers\AppServiceProvider::class,
    Modules\UserManagement\Providers\UserManagementServiceProvider::class,
    Laravel\Passport\PassportServiceProvider::class,
];
