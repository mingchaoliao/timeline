import {Component, Input, OnInit, ViewChild} from '@angular/core';
import {NgbModal} from '@ng-bootstrap/ng-bootstrap';

@Component({
  selector: 'app-confirmation-modal',
  templateUrl: './confirmation-modal.component.html',
  styleUrls: ['./confirmation-modal.component.css']
})
export class ConfirmationModalComponent implements OnInit {

  @Input('content') content: string;
  @Input('buttons') buttons: Array<any> = [];
  @ViewChild('modal') modal;

  constructor(public modalService: NgbModal) {
  }

  ngOnInit() {
  }

  public open() {
    this.modalService.open(this.modal);
  }

}
