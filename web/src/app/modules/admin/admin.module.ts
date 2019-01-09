import {NgModule} from '@angular/core';
import {CommonModule} from '@angular/common';
import {CreateEventComponent} from './pages/create-event/create-event.component';
import {ImportEventComponent} from './pages/import-event/import-event.component';
import {UpdateEventComponent} from './pages/update-event/update-event.component';
import {RichTextEditorComponent} from './components/rich-text-editor/rich-text-editor.component';
import {EventImageInputComponent} from './components/event-image-input/event-image-input.component';
import {ADMIN_ROUTING} from './admin.route';
import {FormsModule, ReactiveFormsModule} from '@angular/forms';
import {NgSelectModule} from '@ng-select/ng-select';
import {CoreModule} from '../core/core.module';
import {NgbModule} from '@ng-bootstrap/ng-bootstrap';
import {UserManagementComponent} from './pages/user-management/user-management.component';
import {MomentModule} from "ngx-moment";
import { ConfigurePeriodsComponent } from './pages/configure-periods/configure-periods.component';
import { ConfigureCatalogsComponent } from './pages/configure-catalogs/configure-catalogs.component';
import { ConfigureDateAttributesComponent } from './pages/configure-date-attributes/configure-date-attributes.component';
import { ConfirmationModalComponent } from './components/confirmation-modal/confirmation-modal.component';

@NgModule({
    imports: [
        CommonModule,
        ADMIN_ROUTING,
        FormsModule,
        ReactiveFormsModule,
        NgSelectModule,
        CoreModule,
        MomentModule,
        NgbModule
    ],
    declarations: [
        CreateEventComponent,
        ImportEventComponent,
        UpdateEventComponent,
        RichTextEditorComponent,
        EventImageInputComponent,
        UserManagementComponent,
        ConfigurePeriodsComponent,
        ConfigureCatalogsComponent,
        ConfigureDateAttributesComponent,
        ConfirmationModalComponent
    ],
    exports: []
})
export class AdminModule {
}
