import {AfterViewInit, Component, forwardRef, OnInit} from '@angular/core';
import {ControlValueAccessor, NG_VALUE_ACCESSOR} from '@angular/forms';

declare var $: any;

@Component({
  selector: 'app-rich-text-editor',
  templateUrl: './rich-text-editor.component.html',
  styleUrls: ['./rich-text-editor.component.css'],
  providers: [
    {
      provide: NG_VALUE_ACCESSOR,
      useExisting: forwardRef(() => RichTextEditorComponent),
      multi: true
    }
  ]
})
export class RichTextEditorComponent implements OnInit, ControlValueAccessor, AfterViewInit {

  private editor;
  private initialData;
  private isDisplayed;
  private propagateChange = (_: any) => {
  };
  private propagateTouch = (_: any) => {
  };


  constructor() {

  }

  ngOnInit() {

  }

  registerOnChange(fn: any): void {
    this.propagateChange = fn;
  }

  registerOnTouched(fn: any): void {
    this.propagateTouch = fn;
  }

  setDisabledState(isDisabled: boolean): void {
    if (this.editor) {
      this.editor.summernote('disable');
    } else {
      this.isDisplayed = isDisabled;
    }
  }

  writeValue(obj: any): void {
    if (this.editor) {
      this.editor.summernote('code', obj);
    } else {
      this.initialData = obj;
    }
  }

  ngAfterViewInit(): void {
    this.editor = $('div.rich-text-editor');
    this.editor.summernote({
      toolbar: [
        ['insert', ['link', 'table', 'hr', 'picture']],
        ['style', ['bold', 'italic', 'underline', 'strikethrough', 'clear']],
        // ['fontsize', ['fontsize']],
        ['color', ['color']],
        ['para', ['ul', 'ol', 'paragraph']],
        ['misc', ['redo', 'undo', 'help']],
      ],
      tabsize: 2,
      height: 100
    });

    if (this.isDisplayed) {
      this.setDisabledState(this.isDisplayed);
    }

    if (this.initialData) {
      this.writeValue(this.initialData);
    }

    this.editor.on('summernote.change', (we, contents, $editable) => {
      if (contents === '<p><br></p>') {
        this.editor.summernote('code', '');
      }
      this.propagateChange(this.editor.summernote('code'));
    });
  }
}
