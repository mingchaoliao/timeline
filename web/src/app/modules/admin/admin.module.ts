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

@NgModule({
  imports: [
    CommonModule,
    ADMIN_ROUTING,
    FormsModule,
    ReactiveFormsModule,
    NgSelectModule,
    CoreModule,
    NgbModule
  ],
  declarations: [
    CreateEventComponent,
    ImportEventComponent,
    UpdateEventComponent,
    RichTextEditorComponent,
    EventImageInputComponent
  ],
  exports: []
})
export class AdminModule {
}
