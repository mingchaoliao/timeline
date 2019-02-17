import {environment} from '../../../../../environments/environment';

export class Url {
  private static getBase(withPrefix = true): string {
    const host = environment.apiHost.replace(/\/+$/, '');
    if (withPrefix) {
      const prefix = environment.apiPrefix.replace(/^\/+|\/+$/, '');
      return `${host}/${prefix}`;
    } else {
      return host;
    }
  }

  private static getUrl(path: string, withPrefix = true): string {
    path = path.replace(/^\/+|\/+$/, '');
    return Url.getBase(withPrefix) + '/' + path;
  }

  public static login(): string {
    return Url.getUrl('/login');
  }

  public static getCurrentUser(): string {
    return Url.getUrl('/user/current');
  }

  static register(): string {
    return Url.getUrl('/register');
  }

  static getEvents(): string {
    return Url.getUrl('/event');
  }

  static getAllUser(): string {
    return Url.getUrl('/user');
  }

  static updateUser(id: number): string {
    return Url.getUrl(`/user/${id}`);
  }

  static getTempImage(path: string): string {
    return Url.getUrl(`/admin/images/${path}`, false);
  }

  static getImage(path: string): string {
    return Url.getUrl(`/storage/images/${path}`, false);
  }

  static getEventById(id: number): string {
    return Url.getUrl('/event/' + id);
  }

  static getPeriod(): string {
    return Url.getUrl('/period');
  }

  static updatePeriod(id: number): string {
    return Url.getUrl('/period/' + id);
  }

  static deletePeriod(id: number): string {
    return Url.getUrl('/period/' + id);
  }

  static updateCatalog(id: number): string {
    return Url.getUrl('/catalog/' + id);
  }

  static deleteCatalog(id: number): string {
    return Url.getUrl('/catalog/' + id);
  }

  static getPeriodTypeahead(): string {
    return Url.getUrl('/period/typeahead');
  }

  static createPeriod(): string {
    return Url.getUrl('/period');
  }

  static bulkCreatePeriod(): string {
    return Url.getUrl('/period/bulk');
  }

  static getCatalogTypeahead(): string {
    return Url.getUrl('/catalog/typeahead');
  }

  static getCatalog(): string {
    return Url.getUrl('/catalog');
  }

  static createCatalog(): string {
    return Url.getUrl('/catalog');
  }

  static bulkCreateCatalog(): string {
    return Url.getUrl('/catalog/bulk');
  }

  static getDateAttributeTypeahead(): string {
    return Url.getUrl('/dateAttribute/typeahead');
  }

  static getDateAttribute(): string {
    return Url.getUrl('/dateAttribute');
  }

  static createDateAttribute(): string {
    return Url.getUrl('/dateAttribute');
  }

  static updateDateAttribute(id: number): string {
    return Url.getUrl('/dateAttribute/' + id);
  }

  static deleteDateAttribute(id: number): string {
    return Url.getUrl('/dateAttribute/' + id);
  }

  static bulkCreateDateAttribute(): string {
    return Url.getUrl('/dateAttribute/bulk');
  }

  static getDateFormat(): string {
    return Url.getUrl('/dateFormat');
  }

  static getTimeline(): string {
    return Url.getUrl('/storage/timeline.json', false);
  }

  static createEvent(): string {
    return Url.getUrl('/event');
  }

  static updateEventById(id: number): string {
    return Url.getUrl('/event/' + id);
  }

  static bulkCreateEvents(): string {
    return Url.getUrl('/event/bulk');
  }

  static uploadImage(): string {
    return Url.getUrl('/image');
  }

  static searchEvent(): string {
    return Url.getUrl('/event/search');
  }

  static deleteEvent(id: number): string {
    return Url.getUrl('/event/' + id);
  }

  static updateImageDescription(id: number): string {
    return Url.getUrl('/image/' + id);
  }

  static getAllBackup() {
    return Url.getUrl('/backup');
  }
}
