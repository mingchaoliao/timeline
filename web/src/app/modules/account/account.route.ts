import {RouterModule, Routes} from '@angular/router';
import {LoginComponent} from './login/login.component';
import {SignupComponent} from './signup/signup.component';
import {ProfileComponent} from "./profile/profile.component";
import {AuthGuard} from "../../auth-guard";

const ACCOUNT_ROUTES: Routes = [
  {path: 'login', component: LoginComponent},
  {path: 'signup', component: SignupComponent},
  {path: 'profile', component: ProfileComponent, canActivate: [AuthGuard]},
];

export const ACCOUNT_ROUTING = RouterModule.forChild(ACCOUNT_ROUTES);
