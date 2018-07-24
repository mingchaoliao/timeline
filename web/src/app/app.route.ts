import {RouterModule, Routes} from '@angular/router';
import {ErrorComponent} from './modules/core/pages/error/error.component';
import {HomeComponent} from './modules/home/home.component';
import {AdminGuard} from './admin-guard';

const APP_ROUTES: Routes = [
  {path: 'admin', loadChildren: './modules/admin/admin.module#AdminModule', canActivate: [AdminGuard]},
  {path: 'app', loadChildren: './modules/guest/guest.module#GuestModule'},
  {path: 'account', loadChildren: './modules/account/account.module#AccountModule'},
  {path: '', component: HomeComponent},
  {path: '**', component: ErrorComponent}
];

export const APP_ROUTING = RouterModule.forRoot(APP_ROUTES);
