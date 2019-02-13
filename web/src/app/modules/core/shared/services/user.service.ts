import {Injectable} from '@angular/core';
import {Url} from '../classes/url';
import {HttpService} from './http.service';
import {User} from '../models/user';
import {Observable} from 'rxjs';
import {Token, TokenType} from '../models/token';
import {Router} from '@angular/router';

@Injectable()
export class UserService {
    private static currentUser: User = null;

    constructor(
        private httpService: HttpService,
        private router: Router
    ) {

    }

    public static getCurrentUser(): User {
        return UserService.currentUser;
    }

    static clearCurrentUser() {
        UserService.currentUser = null;
    }

    public getAll(): Observable<Array<User>> {
        return new Observable<Array<User>>(
            observer => {
                this.httpService.get(Url.getAllUser()).subscribe(
                    responseBody => {
                        observer.next(User.fromArray(<Array<any>>responseBody));
                    },
                    error => {
                        observer.error(error);
                    },
                    () => {
                        observer.complete();
                    }
                );
            }
        );
    }

    public static logout() {
        localStorage.clear();
        UserService.currentUser = null;
    }

    public grantOrRevokeAdminPrivilege(id: number, isAdmin: boolean): Observable<boolean> {
        return new Observable<boolean>(observer => {
            this.httpService.put(Url.updateUser(id), {}, {
                isAdmin: isAdmin
            }).subscribe(
                responseBody => {
                    observer.next(<boolean>responseBody);
                },
                error => {
                    observer.error(error);
                },
                () => {
                    observer.complete();
                }
            );
        });
    }

    public grantOrRevokeEditorPrivilege(id: number, isEditor: boolean): Observable<boolean> {
        return new Observable<boolean>(observer => {
            this.httpService.put(Url.updateUser(id), {}, {
                isEditor: isEditor
            }).subscribe(
                responseBody => {
                    observer.next(<boolean>responseBody);
                },
                error => {
                    observer.error(error);
                },
                () => {
                    observer.complete();
                }
            );
        });
    }

    public activateOrInactivateAccount(id: number, isActive: boolean): Observable<boolean> {
        return new Observable<boolean>(observer => {
            this.httpService.put(Url.updateUser(id), {}, {
                isActive: isActive
            }).subscribe(
                responseBody => {
                    observer.next(<boolean>responseBody);
                },
                error => {
                    observer.error(error);
                },
                () => {
                    observer.complete();
                }
            );
        });
    }

    public updateProfile(id: number, name: string): Observable<User> {
        return new Observable<User>(observer => {
            this.httpService.put(Url.updateUser(id), {}, {
                name: name
            }).subscribe(
                responseBody => {
                    observer.next(User.fromJson(responseBody));
                },
                error => {
                    observer.error(error);
                },
                () => {
                    observer.complete();
                }
            );
        });
    }

    public updatePassword(id: number, oldPassword: string, newPassword: string): Observable<boolean> {
        return new Observable<boolean>(observer => {
            this.httpService.put(Url.updateUser(id), {}, {
                oldPassword: oldPassword,
                newPassword: newPassword
            }).subscribe(
                responseBody => {
                    observer.next(<boolean>responseBody);
                },
                error => {
                    observer.error(error);
                },
                () => {
                    observer.complete();
                }
            );
        });
    }

    public static getAccessTokenFromLocalStorage(): Token {
        const tokenStr: string = localStorage.getItem('access_token');
        if (!tokenStr) {
            return null;
        }
        const token: Token = new Token(
            tokenStr,
            TokenType.Bearer
        );
        if (token.isTokenExpired()) {
            return null;
        }
        return token;
    }

    public login(email: string, password: string): Observable<User> {
        return new Observable<User>(
            observer => {
                this.httpService.post(Url.login(), {}, {
                    email: email,
                    password: password
                }).subscribe(
                    responseBody => {
                        try {
                            localStorage.setItem('access_token', responseBody['token']);
                            this.getCurrentUser(true).subscribe(
                                user => UserService.currentUser = User.fromJson(user)
                            );
                            observer.next(UserService.currentUser);
                        } catch (e) {
                            observer.error(e);
                        }
                    },
                    error => {
                        observer.error(error);
                    },
                    () => {
                        observer.complete();
                    }
                );
            }
        );
    }

    public register(name: string, email: string, password: string): Observable<User> {
        return new Observable<User>(
            observer => {
                this.httpService.post(Url.register(), {}, {
                    name: name,
                    email: email,
                    password: password
                }).subscribe(
                    responseBody => {
                        try {
                            UserService.currentUser = User.fromJson(responseBody);
                            localStorage.setItem('access_token', responseBody['accessToken']);
                            observer.next(UserService.currentUser);
                        } catch (e) {
                            observer.error(e);
                        }
                    },
                    error => {
                        observer.error(error);
                    },
                    () => {
                        observer.complete();
                    }
                );
            }
        );
    }

    public getCurrentUser(forceRefresh: boolean = false): Observable<User> {
        return new Observable<User>(
            observer => {
                const accessToken: Token = UserService.getAccessTokenFromLocalStorage();

                if (UserService.currentUser === null && !accessToken) {
                    observer.error(new Error('Current currentUser did not login to the application'));
                    observer.complete();
                } else if (UserService.currentUser && !forceRefresh) {
                    observer.next(UserService.currentUser);
                    observer.complete();
                } else {
                    this.httpService.get(Url.getCurrentUser()).subscribe(
                        responseBody => {
                            try {
                                UserService.currentUser = User.fromJson(responseBody);
                                observer.next(UserService.currentUser);
                            } catch (e) {
                                observer.error(e);
                            }
                        },
                        error => {
                            observer.error(error);
                        },
                        () => {
                            observer.complete();
                        }
                    );
                }
            }
        );
    }

    setCurrentUser(user: User) {
        UserService.currentUser = user;
    }
}
