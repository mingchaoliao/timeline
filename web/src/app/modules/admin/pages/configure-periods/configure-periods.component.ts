import { Component, OnInit } from '@angular/core';
import {PeriodService} from "../../../core/shared/services/period.service";
import {Period} from "../../../core/shared/models/period";

@Component({
  selector: 'app-configure-periods',
  templateUrl: './configure-periods.component.html',
  styleUrls: ['./configure-periods.component.css']
})
export class ConfigurePeriodsComponent implements OnInit {

  public periods: Array<Period> = null;

  constructor(public periodService: PeriodService) {
    periodService.get().subscribe(
        s => {
          this.periods = s;
        }
    );
  }

  ngOnInit() {
  }

}
