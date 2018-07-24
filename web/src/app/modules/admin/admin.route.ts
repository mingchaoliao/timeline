import {RouterModule, Routes} from '@angular/router';
import {CreateEventComponent} from './pages/create-event/create-event.component';
import {UpdateEventComponent} from './pages/update-event/update-event.component';
import {ImportEventComponent} from './pages/import-event/import-event.component';

const ADMIN_ROUTES: Routes = [
  {path: 'create-event', component: CreateEventComponent},
  {path: 'event/:id/update', component: UpdateEventComponent},
  {path: 'import-event', component: ImportEventComponent}
];

export const ADMIN_ROUTING = RouterModule.forChild(ADMIN_ROUTES);
