export const cmn_mixin = {
    methods: {
        get_zeiko_url(id){
            return root_path + 'admin/zaiko/index/' + id;
        },
        get_kzeiko_url(id){
            return root_path + 'admin/kzaiko/index/?search_zaiko_id=' + id + '&search_sort=2';
        },
    },
    computed:{
		IsMobile(){
            return this.$mq === 'sp';
		},
    },
}
