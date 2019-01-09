import {AfterViewInit, Directive, ElementRef, OnDestroy} from '@angular/core';

@Directive({
  selector: '[appAutofocus]'
})
export class AutofocusDirective implements AfterViewInit, OnDestroy {

  private timeoutId: number = null;

  constructor(public el: ElementRef) {
  }

  ngAfterViewInit() {
    this.timeoutId = setTimeout(this.el.nativeElement.focus(), 10);
  }

  ngOnDestroy() {
    if (this.timeoutId !== null) {
      clearTimeout(this.timeoutId);
    }
  }
}
