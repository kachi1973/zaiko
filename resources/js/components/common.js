import VueMq from 'vue-mq'
import moment from 'moment';

const vue_set_cmn = (Vue) => {
    Vue.filter('YYYYMMDD', function (value) {
        if (value != null) {
            return moment(value).format('YYYY/MM/DD');
        }
        return null;
    });
    Vue.filter('addComma', function(val){
        if(val){
            return val.toLocaleString();
        }
        return "";
    });
    Vue.use(VueMq, {
        breakpoints: {
        sp: 767,
        pc: 768
        },
        defaultBreakpoint: 'sp'
    });
}
export { vue_set_cmn };

const jquery_set_cmn = ($) => {
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });
    $(document).ajaxError(function(e, xhr, opts, error) {
        switch(xhr.status){
            case 419:
                alert(`タイムアウトによりログアウトしました。\n{error}`);
                window.location.href = root_path;
                break;
        }
    });
}
export { jquery_set_cmn };

const get_tana_name = (zaiko) => {
    return `${zaiko.jigyosyo_name}(${zaiko.jigyosyo_tana_id}):${zaiko.basho}:${zaiko.basho_no}:${zaiko.basho_tana}`;
}
export { get_tana_name };

const get_zaiko_name = (zaiko) => {
    let strs = [zaiko.jigyosyo_name, zaiko.jigyosyo_tana_id, zaiko.basho, zaiko.basho_no, zaiko.basho_tana, zaiko.hinmoku_id, zaiko.hinmoku.model];
    if(zaiko.model_v) strs.push(zaiko.model_v)
    if(zaiko.model_kind) strs.push(zaiko.model_kind)
    return `[${zaiko.id}]${zaiko.jigyosyo_name}(${zaiko.jigyosyo_tana_id}):${strs.join(':')}`;
}
export { get_zaiko_name };
