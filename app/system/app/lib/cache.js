import storage from 'JSONStorage';

export default function (bucket, adapter) {
    const db = storage.select(bucket, adapter || 'local');

    return {

        set(key, value, minutes) {
            if (minutes) {
                return db.setex(key, minutes * 60, value);
            }
            return db.set(key, value);
        },

        get() {
            return db.get.apply(db, arguments);
        },

        remove(key) {
            return db.del.apply(db, arguments);
        },

        flush() {
            return db.flushdb.apply(db, arguments);
        },

    };
}
