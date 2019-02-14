import {BrowserModule} from '@angular/platform-browser';
import {NgModule} from '@angular/core';
import {NgbModule} from '@ng-bootstrap/ng-bootstrap';

import {AppComponent} from './app.component';
import {APP_ROUTING} from './app.route';
import {HomeModule} from './modules/home/home.module';
import {JwtModule} from '@auth0/angular-jwt';
import {CoreModule} from './modules/core/core.module';
import {HttpClientModule} from '@angular/common/http';
import {environment} from '../environments/environment';
import {AdminGuard} from './admin-guard';
import {EditorGuard} from './editor-guard';
import {AuthGuard} from "./auth-guard";

export function tokenGetter() {
  return localStorage.getItem('access_token');
}

@NgModule({
  declarations: [
    AppComponent
  ],
  imports: [
    BrowserModule,
    APP_ROUTING,
    NgbModule.forRoot(),
    HomeModule,
    HttpClientModule,
    JwtModule.forRoot({
      config: {
        headerName: 'Authorization',
        tokenGetter: tokenGetter,
        whitelistedDomains: environment.whitelistedDomains,
        authScheme: 'Bearer ',
        skipWhenExpired: true
      }
    }),
    CoreModule
  ],
  providers: [
    AdminGuard,
    EditorGuard,
    AuthGuard
  ],
  bootstrap: [AppComponent]
})
export class AppModule {
}
