import { Component, OnInit } from '@angular/core';
import {NgbModal, NgbModalRef} from "@ng-bootstrap/ng-bootstrap";
import {DateAttributeService} from "../../../core/shared/services/dateAttribute.service";
import {DateAttribute} from "../../../core/shared/models/dateAttribute";

@Component({
  selector: 'app-configure-date-attributes',
  templateUrl: './configure-date-attributes.component.html',
  styleUrls: ['./configure-date-attributes.component.css']
})
export class ConfigureDateAttributesComponent implements OnInit {
    public dateAttributes: Array<DateAttribute> = null;
    public createModalRef: NgbModalRef = null;

    constructor(
        public dateAttributeService: DateAttributeService,
        public modalService: NgbModal
    ) {
        dateAttributeService.get().subscribe(
            s => {
                this.dateAttributes = s;
            }
        );
    }

    public delete(index: number) {
        const dateAttribute = this.dateAttributes[index];
        this.dateAttributeService.delete(dateAttribute.id).subscribe(
            s => {
                this.dateAttributes.splice(index, 1);
            }
        );
    }

    public update(index: number, value: string) {
        const dateAttribute = this.dateAttributes[index];
        this.dateAttributeService.update(dateAttribute.id, value).subscribe(
            s => {
                this.dateAttributes[index] = s;
            }
        );
    }

    public createNew(value: string) {
        this.dateAttributeService.create(value).subscribe(
            s => {
                this.dateAttributes.push(s);
                this.createModalRef.close();
            }
        )
    }

    ngOnInit() {
    }
}
