import {Component, OnInit} from '@angular/core';
import {PeriodService} from "../../../core/shared/services/period.service";
import {Period} from "../../../core/shared/models/period";
import {NgbModal, NgbModalRef} from "@ng-bootstrap/ng-bootstrap";

@Component({
  selector: 'app-configure-periods',
  templateUrl: './configure-periods.component.html',
  styleUrls: ['./configure-periods.component.css']
})
export class ConfigurePeriodsComponent implements OnInit {

  public periods: Array<Period> = null;
  public createModalRef: NgbModalRef = null;

  constructor(
      public periodService: PeriodService,
      public modalService: NgbModal
  ) {
    periodService.get().subscribe(
        s => {
          this.periods = s;
        }
    );
  }

  public delete(index: number) {
    const period = this.periods[index];
    this.periodService.delete(period.id).subscribe(
        s => {
          this.periods.splice(index, 1);
        }
    );
  }

  public update(index: number, value: string) {
      const period = this.periods[index];
      this.periodService.update(period.id, value).subscribe(
          s => {
              this.periods[index] = s;
          }
      );
  }

  public createNew(value: string) {
    this.periodService.create(value).subscribe(
        s => {
          this.periods.push(s);
          this.createModalRef.close();
        }
    )
  }

  ngOnInit() {
  }

}
