import { Component, OnInit } from '@angular/core';
import {Catalog} from "../../../core/shared/models/catalog";
import {NgbModal, NgbModalRef} from "@ng-bootstrap/ng-bootstrap";
import {CatalogService} from "../../../core/shared/services/catalog.service";

@Component({
  selector: 'app-configure-catalogs',
  templateUrl: './configure-catalogs.component.html',
  styleUrls: ['./configure-catalogs.component.css']
})
export class ConfigureCatalogsComponent implements OnInit {
    public catalogs: Array<Catalog> = null;
    public createModalRef: NgbModalRef = null;

    constructor(
        public catalogService: CatalogService,
        public modalService: NgbModal
    ) {
        catalogService.get().subscribe(
            s => {
                this.catalogs = s;
            }
        );
    }

    public delete(index: number) {
        const catalog = this.catalogs[index];
        this.catalogService.delete(catalog.id).subscribe(
            s => {
                this.catalogs.splice(index, 1);
            }
        );
    }

    public update(index: number, value: string) {
        const catalog = this.catalogs[index];
        this.catalogService.update(catalog.id, value).subscribe(
            s => {
                this.catalogs[index] = s;
            }
        );
    }

    public createNew(value: string) {
        this.catalogService.create(value).subscribe(
            s => {
                this.catalogs.push(s);
                this.createModalRef.close();
            }
        )
    }

    ngOnInit() {
    }

}
