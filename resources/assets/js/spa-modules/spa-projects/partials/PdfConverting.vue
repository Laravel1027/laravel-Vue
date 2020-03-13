<template>
    <div class="pdfLoader">
        <el-row type="flex" justify="center" align="middle" style="flex-wrap:wrap; width: 85%">
            <p v-if="progress">Please wait for your PDF to upload and compress. Please keep browser open while PDF is uploading.</p>
            <p v-else>DONE!</p>
            <el-col :sm="24" style="margin-top: 10px">
                <el-progress :text-inside="true" :stroke-width="18"
                             :percentage="percentage" color="#80B4A0">
                </el-progress>
            </el-col>
            <el-col :sm="24">
                <div style="text-align: center;">
                    <template v-if="status == 'uploaded'">
                        <p class="status-info"><i class="el-icon-loading p-spinner"></i>READING PDF FILE</p>
                    </template>
                    <template v-else-if="status == 'converting'">
                        <template v-if="progress">
                            <p style="font-size: 13px; color: #80B4A0">TOTAL PAGES: <b>{{totalPages}}</b></p>
                            <p style="font-size: 13px; color: #80B4A0">CONVERTED: <b>{{convertedPages}}</b></p>
                            <p style="font-size: 13px; color: #80B4A0">FAILED: <b>{{failedPages}}</b></p>
                        </template>
                        <template v-else>
                            <p style="font-size: 13px; color: #80B4A0">TOTAL PAGES CONVERTED: <b>{{convertedPages}} from {{totalPages}}</b></p>
                            <p v-if="unconvertedPages.length" style="font-size: 13px; color: #80B4A0">FAILED TO CONVERT PAGES: <b v-for="page in unconvertedPages" :key="page">{{page}} </b></p>
                            <el-button type="primary" @click="confirm">OK</el-button>
                        </template>
                    </template>
                    <template v-else>
                        <p class="status-info"><i class="el-icon-loading p-spinner"></i> UPLOADING PDF FILE</p>
                    </template>
                </div>
            </el-col>
        </el-row>
        <!--<el-col :xs="24" :md="8" :offset="8" style="margin-top: 20px">-->
            <!--<el-button @click="cancel()">Cancel</el-button>-->
        <!--</el-col>-->
    </div>
</template>

<script>
    export default {
        name: "PdfConverting",
        data() {
            return {
                pdfUploaded: false,
                progress: true,
                convertedPages: 0,
                failedPages: 0,
                unconvertedPages: [],
                totalPages: 0,
                percentage: 0,
                status: 'uploading'
            }
        },
        methods: {
            confirm() {
                this.$emit('confirm');
            }
            // cancel() {
            //     this.$emit('cancelUpload')
            // }
        },
        created() {
            //Listen to pdf uplaoded event
            Echo.private('pdf-uploaded')
                .listen('PdfUploaded', (e) => {
                    this.status = 'uploaded';
                });

            //Listen to pdf readed event
            Echo.private('pdf-readed')
                .listen('PdfReaded', (e) => {
                    this.status = 'converting';
                    this.totalPages = e.totalPages;
                });

            //Listen to pdf page converted event
            Echo.private('pdf-converting')
                .listen('PdfPageConverted', (e) => {
                    if (e.status) {
                        this.convertedPages++
                    } else {
                        this.failedPages++;
                        this.unconvertedPages.push('N' + e.currentPage);
                    }

                    this.percentage = Math.round((e.currentPage / this.totalPages) * 100)
                    if (e.currentPage == this.totalPages) {
                        this.progress = false;
                    }
                });
        }
    }
</script>

<style scoped>
    .pdfLoader {
        background: rgb(255, 255, 255);
        width: 100%;
        height: 100%;
        position: absolute;
        top: 0;
        left: 0;
        z-index: 999;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-wrap: wrap;
    }

    .pdfLoader p {
        text-align: center;
        font-weight: bold;
        color: #80B4A0;
    }

    .status-info {
        font-size: 14px;
        color: #80B4A0;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-top: 10px;
    }

    .p-spinner {
        font-size: 25px !important;
    }
</style>