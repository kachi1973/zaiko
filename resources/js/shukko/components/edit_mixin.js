import * as cmn from '../../components/common.js';

export const edit_mixin = {
    props: {
        sys: null,
	},
    data() {
		return {
            item:{
                ing: false,
                id: null,
                jigyosyo_id: null,
                user_id: null,
                shukko_user_id: null,
                shukko_date: null,
                status10_date: null,
                status20_date: null,
                status30_date: null,
                status40_date: null,
                status50_date: null,
                status60_date: null,
                status: 0,
                seiban: null,
                shukko_user_id: null,
                shukko_user_name: null,
                items: [],
                file_urls: [],
            },
        }
    },
	mounted() {
        var t = this;
        var hit = false;
        if('id' in t.$route.params){
            t.item.id = t.$route.params.id;
            if (0 < t.item.id){
                t.item.ing = true;
                hit = true;
                $.ajax({
                    type : "POST",
                    url : root_path + "shukko/AjaxGetShukko",
                    dataType : "json",
                    contentType : 'application/json',
                    async: true,
                    data : JSON.stringify({
                        id: t.item.id,
                    })
                }).done(function(datas) {
                    t.set_data(datas);
                    t.item.ing = false;
                    t.mounted_complete();
                }).fail(function(jqXHR, textStatus, errorThrown) {
                    t.item.ing = false;
                    t.mounted_complete();
                });
            }
        }
        if (!hit){
            var now = new Date();
            t.item.shukko_date = t.$options.filters.YYYYMMDD(now);
            t.item.user_id = user_id;
            t.sys.bumons.forEach(bumon =>{
                if(bumon.id == user_bumon_id){
                    t.item.jigyosyo_id = bumon.jigyosyo_id;
                }
            });
            t.mounted_complete();
        }
    },
    methods: {
        set_data(datas){
            var t = this;
            t.item.id = datas.item.id;
            t.item.jigyosyo_id = datas.item.jigyosyo_id;
            t.item.jigyosyo_name = datas.item.jigyosyo_name;
            t.item.user_id = datas.item.user_id;
            t.item.user_name = datas.item.user_name;
            t.item.bumon_name = datas.item.bumon_name;
            t.item.shukko_user_id = datas.item.shukko_user_id;
            t.item.status = datas.item.status;
            t.item.status_str = datas.item.status_str;
            t.item.seiban = datas.item.seiban;
            t.item.shukko_user_id = datas.item.shukko_user_id;
            t.item.shukko_user_name = datas.item.shukko_user_name;
            t.item.items.splice(0, t.item.items.length);
            datas.item.items.forEach(item => {
                item.desc_flg = 0<item.zaiko.kobetu_flg && 10<=t.item.status;
            });
            t.item.items.push(...datas.item.items);
            t.item.file_urls.splice(0, t.item.file_urls.length);
            t.item.file_urls.push(...datas.item.file_urls);
            t.set_data2(datas);
        },
        set_data2(datas) {
            var t = this;
            t.item.shukko_date = t.$options.filters.YYYYMMDD(datas.item.shukko_date);
            t.item.status10_date = t.$options.filters.YYYYMMDD(datas.item.status10_date);
            t.item.status10_user_name = datas.item.status10_user_name;
            t.item.status20_date = t.$options.filters.YYYYMMDD(datas.item.status20_date);
            t.item.status20_user_name = datas.item.status20_user_name;
            t.item.status30_date = t.$options.filters.YYYYMMDD(datas.item.status30_date);
            t.item.status30_user_name = datas.item.status30_user_name;
            t.item.status40_date = t.$options.filters.YYYYMMDD(datas.item.status40_date);
            t.item.status40_user_name = datas.item.status40_user_name;
            t.item.status50_date = t.$options.filters.YYYYMMDD(datas.item.status50_date);
            t.item.status50_user_name = datas.item.status50_user_name;
            t.item.status60_date = t.$options.filters.YYYYMMDD(datas.item.status60_date);
            t.item.status60_user_name = datas.item.status60_user_name;
            t.item.status70_date = t.$options.filters.YYYYMMDD(datas.item.status70_date);
            t.item.status70_user_name = datas.item.status70_user_name;
        },
        mounted_complete(){
        },
        chk_data(type){
            var t = this;
            var err_dlg = (message)=>{
                t.$dialog.alert({
                    title: 'エラー',
                    body: message,
                }, {
                    okText: 'ok',
                    reverse: false,
                });
            };
            switch(t.item.status){
                case 0: // 申請待ち
                    if(t.item.items.some(rec => {
                        // 事業所
                        if(rec.zaiko.jigyosyo_id != t.item.jigyosyo_id){
                            err_dlg('事業所が混在しています。');
                            return true;
                        } else if (!Number.isInteger(rec.req_suu) || rec.req_suu<1) {
                            err_dlg('申請数が不正な部品があります。');
                            return true;
                        }else if(rec.zaiko.zaiko_suu - rec.zaiko.sinsei_suu - rec.zaiko.kashi_suu - rec.req_suu < 0){
                            err_dlg('申請数が上限を超えている部品があります。');
                            return true;
                        }
                        return false;
                    })){
                        return false;
                    }
                    break;
                case 10: // 出庫待ち
                    var total = 0;
                    if (t.item.items.some(rec => {
                        if (!Number.isInteger(rec.out_suu) || rec.req_suu < rec.out_suu){
                            err_dlg('許可数が不正な部品があります。');
                            return true;
                        }
                        total += rec.out_suu;
                        return false;
                    })) {
                        return false;
                    }
                    if (total==0){
                        err_dlg('許可数がゼロです。');
                        return false;
                    }
                    break;
                case 20: // 返却待ち
                    if (t.item.seiban == null || t.item.seiban.length == 0) {
                        err_dlg('受注番号を入力して下さい。');
                        return false;
                    }
                    if (t.item.items.some(rec => {
                        if (!Number.isInteger(rec.used_suu) || rec.out_suu < rec.used_suu){
                            err_dlg('使用数が不正な部品があります。');
                            return true;
                        }
                        return false;
                    })) {
                        return false;
                    }
                    break;
                case 40: // 製番入力待ち
                    var total = 0;
                    if (t.item.items.some(rec => {
                        if (rec.zaiko.scaw_flg==1){
                            if (0 < rec.used_suu){
                                if (rec.seiban == null || rec.seiban.length==0) {
                                    err_dlg('投入製番を入力して下さい。');
                                    return true;
                                }
                                if (t.item.file_urls.length == 0) {
                                    err_dlg('ファイルをアップロードしてください。');
                                    return true;
                                }
                            }
                        }
                        total += rec.used_suu;
                        return false;
                    })) {
                        return false;
                    }
                    break;
            }
            return true;
        },
        warn_chk_data(type){
            var t = this;
            var mess = '登録してもよろしいですか？';
            switch(t.item.status){
                case 10: // 出庫待ち
                    t.item.items.some(rec => {
                        if (rec.out_suu < rec.req_suu){
                            mess = '申請数より許可数が少ない部品があります。登録してもよろしいですか？';
                            return true;
                        }
                    });
                    break;
                case 20: // 返却待ち
                    t.item.items.some(rec => {
                        if (rec.used_suu < rec.out_suu){
                            mess = '使用数が許可数より少ない部品があります。登録してもよろしいですか？';
                            return true;
                        }
                    });
                    break;
            }
            return mess;
        },
    },
}
