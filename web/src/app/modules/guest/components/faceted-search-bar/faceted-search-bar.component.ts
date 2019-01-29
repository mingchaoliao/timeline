import {Component, EventEmitter, Input, OnInit, Output} from '@angular/core';
import {Bucket} from '../../../core/shared/models/eventSearchResult';

export interface Facet {
  buckets: Array<Bucket>;
  name: string;
  numDisplayed: number;
  increment: number;
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

  constructor() {
  }

  ngOnInit() {
  }

}
