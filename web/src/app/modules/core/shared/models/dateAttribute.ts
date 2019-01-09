export class DateAttribute {
    private readonly _id: number;
    private readonly _value: string;
    private readonly _createUserId: number;
    private readonly _createUserName: string;
    private readonly _updateUserId: number;
    private readonly _updateUserName: string;
    private readonly _createdAt: Date;
    private readonly _updatedAt: Date;

    constructor(id: number, value: string, createUserId: number, createUserName: string, updateUserId: number, updateUserName: string, createdAt: Date, updatedAt: Date) {
        this._id = id;
        this._value = value;
        this._createUserId = createUserId;
        this._createUserName = createUserName;
        this._updateUserId = updateUserId;
        this._updateUserName = updateUserName;
        this._createdAt = createdAt;
        this._updatedAt = updatedAt;
    }

    get id(): number {
        return this._id;
    }

    get value(): string {
        return this._value;
    }

    get createUserId(): number {
        return this._createUserId;
    }

    get createUserName(): string {
        return this._createUserName;
    }

    get updateUserId(): number {
        return this._updateUserId;
    }

    get updateUserName(): string {
        return this._updateUserName;
    }

    get createdAt(): Date {
        return this._createdAt;
    }

    get updatedAt(): Date {
        return this._updatedAt;
    }

    static fromJson(json: any): DateAttribute {
        if (json === null) {
            return null;
        }
        return new DateAttribute(
            json['id'],
            json['value'],
            json['createUserId'],
            json['createUserName'],
            json['updateUserId'],
            json['updateUserName'],
            json['createdAt'],
            json['updatedAt']
        );
    }

    static fromArray(arr: Array<any>): Array<DateAttribute> {
        if (arr === null) {
            return [];
        }
        return arr.map((json: any) => {
            return DateAttribute.fromJson(json);
        });
    }
}