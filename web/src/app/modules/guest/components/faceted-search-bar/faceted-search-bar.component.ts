import {Component, EventEmitter, Input, OnInit, Output} from '@angular/core';
import {Bucket} from '../../../core/shared/models/eventSearchResult';

export interface Facet {
  buckets: Array<Bucket>;
  name: string;
  numDisplayed: number;
  increment: number;
  disable: Array<string>;
  onClick: (value) => void;
}

export interface FacetLink {
  facetIndex: number;
  value: string;
}

@Component({
  selector: 'app-faceted-search-bar',
  templateUrl: './faceted-search-bar.component.html',
  styleUrls: ['./faceted-search-bar.component.css']
})
export class FacetedSearchBarComponent implements OnInit {

  @Input('facets') facets: Array<Facet>;
  @Output('onFacetChange') onChange: EventEmitter<FacetLink> = new EventEmitter<FacetLink>();

  public status: any = {};

  constructor() {
  }

  ngOnInit() {
    for (let i = 0; i < this.facets.length; i++) {
      this.status[i] = this.facets[i].numDisplayed;
    }
  }

  more(index: number, facet: Facet) {
    this.status[index] = this.status[index] + facet.increment;
    if (this.status[index] > facet.buckets.length) {
      this.status[index] = facet.buckets.length;
    }
  }

  less(index: number, facet: Facet) {
    this.status[index] = this.status[index] - facet.increment;
    if (this.status[index] <= 1) {
      this.status[index] = 1;
    }
  }
}
