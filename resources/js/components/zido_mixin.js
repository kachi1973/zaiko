import { suppressDeprecationWarnings } from 'moment';
import * as cmn from './common.js';

export const zido_mixin = {
    props: {
        sys: null,
        id: "",
        stsnum: "",
        suffix: "",
        texttype: {
            type: Number,
            default: 1,
        },
        disabled: false,
        value: false,
    },
    computed: {
        clsname() {
            switch (this.stsnum) {
                case 10:
                    return 'status10';
                case 20:
                    return 'status20';
                case 30:
                    return 'status30';
                case 40:
                    return 'status40';
                case 50:
                    return 'status50';
                case 60:
                    return 'status60';
                case 70:
                    return 'status70';
                case 99:
                    return 'status99';
                default:
                    return 'status00';
            }
        },
        statusname() {
            var t = this;
            switch (t.texttype){
                case 1:
                    switch (t.stsnum) {
                        case 10:
                            return '課長承認待';
                        case 20:
                            return '出庫待';
                        case 30:
                            return '入庫待';
                        case 40:
                        case 50:
                        case 60:
                        case 70:
                                return '完了';
                        case 99:
                            return 'キャンセル';
                        default:
                            return '申請待';
                    }
                    break;
                case 2:
                    switch (t.stsnum) {
                        case 10:
                            return '課長承認';
                        case 20:
                            return '出庫完了';
                        case 30:
                            return '入庫完了';
                        case 40:
                        case 50:
                        case 60:
                        case 70:
                            return '完了';
                        case 99:
                            return 'キャンセル';
                        default:
                            return '申請';
                    }
                    break;
            }
        }
    },
}
