import {Component, EventEmitter, Input, OnInit, Output, OnDestroy} from '@angular/core';

@Component({
  selector: 'app-button',
  templateUrl: './button.component.html',
  styleUrls: ['./button.component.css']
})
export class ButtonComponent implements OnInit, OnDestroy {

  @Input('style') style: string;
  @Input('disabled') disabled = false;
  @Output('onclick') click: EventEmitter<EventEmitter<boolean>> = new EventEmitter<EventEmitter<boolean>>();

  private loadingEmitter: EventEmitter<boolean> = new EventEmitter<boolean>();
  private loadingEmitterSubscriber = null;
  public isLoading = false;

  constructor() {
  }

  ngOnInit() {
    this.loadingEmitterSubscriber = this.loadingEmitter.subscribe(
      isLoading => this.isLoading = isLoading
    );
  }

  onClick() {
    this.click.emit(this.loadingEmitter);
  }

  ngOnDestroy() {
    if (this.loadingEmitterSubscriber !== null) {
      this.loadingEmitterSubscriber.unsubscribe();
    }
  }
}
