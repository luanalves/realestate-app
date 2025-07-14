<?php

return [
    App\Providers\AppServiceProvider::class,
    Modules\UserManagement\Providers\UserManagementServiceProvider::class,
    Modules\RealEstate\Providers\RealEstateServiceProvider::class,
    Modules\Security\Providers\SecurityServiceProvider::class,
    Modules\Organization\Providers\OrganizationServiceProvider::class,
    Laravel\Passport\PassportServiceProvider::class,
];
