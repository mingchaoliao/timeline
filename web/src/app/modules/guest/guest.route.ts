import {RouterModule, Routes} from '@angular/router';
// import {AboutComponent} from './pages/about/about.component';
import {SearchEventComponent} from './pages/search-event/search-event.component';
import {EventsComponent} from './pages/events/events.component';

const GUEST_ROUTES: Routes = [
  // {path: 'about', component: AboutComponent},
  {path: 'event/search', component: SearchEventComponent},
  {path: 'events', component: EventsComponent},
];

export const GUEST_ROUTING = RouterModule.forChild(GUEST_ROUTES);
