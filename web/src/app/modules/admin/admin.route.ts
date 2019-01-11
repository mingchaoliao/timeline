import {RouterModule, Routes} from '@angular/router';
import {CreateEventComponent} from './pages/create-event/create-event.component';
import {UpdateEventComponent} from './pages/update-event/update-event.component';
import {ImportEventComponent} from './pages/import-event/import-event.component';
import {UserManagementComponent} from "./pages/user-management/user-management.component";
import {ConfigurePeriodsComponent} from "./pages/configure-periods/configure-periods.component";
import {ConfigureCatalogsComponent} from "./pages/configure-catalogs/configure-catalogs.component";
import {ConfigureDateAttributesComponent} from "./pages/configure-date-attributes/configure-date-attributes.component";

const ADMIN_ROUTES: Routes = [
    {path: 'create-event', component: CreateEventComponent},
    {path: 'event/:id/update', component: UpdateEventComponent},
    {path: 'import-event', component: ImportEventComponent},
    {path: 'user-management', component: UserManagementComponent},
    {path: 'configure-periods', component: ConfigurePeriodsComponent},
    {path: 'configure-catalogs', component: ConfigureCatalogsComponent},
    {path: 'configure-date-attributes', component: ConfigureDateAttributesComponent}
];

export const ADMIN_ROUTING = RouterModule.forChild(ADMIN_ROUTES);
