<template>
  <div>
      <h1>Example - Feature 1 page 1</h1>
      <p>{{ hasil }}</p>
      <b-button size="sm" variant="primary" @click="sendApi()">
          cek api
      </b-button>
  </div>
</template>
<script>
export default {
    data() {
      return {
        hasil : 'hasilnya'
      };
    },
    computed: {
        allertData() {
            return this.$store.state.template.alertData;
        }
    },
    methods: {
        sendApi() {            
            this.LocalApi.post('/example/api1').then((res)=>{
              console.log('success : ',res);
            }).catch((err)=>{
              console.log("error : ", err);
            });
        }
    }
}
</script>