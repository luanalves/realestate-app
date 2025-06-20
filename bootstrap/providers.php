<?php

return [
    App\Providers\AppServiceProvider::class,
    Modules\UserManagement\Providers\UserManagementServiceProvider::class,
    Modules\RealEstate\Providers\RealEstateServiceProvider::class,
    Modules\Security\Providers\SecurityServiceProvider::class,
    Laravel\Passport\PassportServiceProvider::class,
];
