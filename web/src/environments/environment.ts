// This file can be replaced during build by using the `fileReplacements` array.
// `ng build ---prod` replaces `environment.ts` with `environment.prod.ts`.
// The list of file replacements can be found in `angular.json`.

export const environment = {
    production: false,
    title: 'Timeline',
    apiHost: 'https://api.timeline.test',
    apiPrefix: 'api',
    languages: [
        {
            lang: 'en',
            cultureLang: 'en-us',
            name: 'English',
            default: false,
        },
        {
            lang: 'zh',
            cultureLang: 'zh-cn',
            name: '简体中文',
            default: true
        },
    ],
    whitelistedDomains: ['api.timeline.test'],
    company: {
        name: 'Mingchao Liao',
        url: 'https://localhot:4200'
    }
};

/*
 * In development mode, to ignore zone related error stack frames such as
 * `zone.run`, `zoneDelegate.invokeTask` for easier debugging, you can
 * import the following file, but please comment it out in production mode
 * because it will have performance impact when throw error
 */
// import 'zone.js/dist/zone-error';  // Included with Angular CLI.
