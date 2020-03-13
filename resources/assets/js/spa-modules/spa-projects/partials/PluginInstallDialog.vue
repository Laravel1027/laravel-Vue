<template>
    <el-dialog :close-on-click-modal="false" :close-on-press-escape="false"
               :show-close="false"
               :visible.sync="promptPluginInstall"
               width="50%" center style="padding: 20px !important;">
        <div style="color: #95989a">
            <el-row type="flex" justify="center">
                <el-col>
                    <el-row style="text-align: center">
                        <h4>Please choose what option below best fits your reviewing needs.</h4>
                    </el-row>

                    <!--Options-->
                    <el-row style="margin-top: 20px" type="flex" justify="center">
                        <!--Plugin-->
                        <el-col :md="12" id="plugin-option">
                            <h3>Option 1</h3>
                            <p>
                                Install Prooflo plugin and start screen capturing your website directly into your
                                Prooflo projects.
                            </p>
                            <div class="plugin-logo">
                                <img src="/img/p.png" alt="p">
                            </div>
                            <el-button class="option-btn"
                                       @click="!pluginInstalled ? installProofloPlugin() : takeScreenshots()" type="primary">
                                <span v-if="!pluginInstalled">INSTALL PLUGIN</span>
                                <span v-else>CAPTURE IMAGE</span>
                            </el-button>
                            <p>
                                <a href="https://prooflo.convertfox.help/article/22-installing-chrome-extension" target="_blank">LEARN MORE</a>
                            </p>
                        </el-col>

                        <!--Upload-->
                        <el-col :md="12" id="upload-option">
                            <h3>Option 2</h3>
                            <p>
                                Upload screen captures from your files directly into your Prooflo projects.
                            </p>
                            <div class="plugin-logo">
                                <el-icon class="el-icon-upload" style="font-size: 80px; color: #58585a"></el-icon>
                            </div>
                            <el-button class="option-btn"
                                       @click="uploadScreens" type="primary">
                                <span>UPLOAD MY SCREENSHOTS</span>
                            </el-button>
                            <p>
                                <a href="https://prooflo.convertfox.help/article/23-uploading-screen-captures" target="_blank">LEARN MORE</a>
                            </p>
                        </el-col>
                    </el-row>
                </el-col>
            </el-row>
            <el-row style="text-align: center">
                <el-col style="margin-top: 20px">
                    <el-button type="primary" @click="closePluginInstallDialog">
                        Close
                    </el-button>
                </el-col>
            </el-row>
        </div>
    </el-dialog>
</template>

<script>
    export default {
        props: ['promptPluginInstall', 'pluginInstalled'],
        data() {
            return {

            }
        },
        methods: {
            closePluginInstallDialog() {
                this.$emit('continue');
            },
            installProofloPlugin() {
                this.$emit('installPlugin');
            },
            takeScreenshots() {
                this.$emit('takeScreenshots');
            },
            uploadScreens() {
                this.$emit('continue');
            },
        }
    }
</script>

<style scoped>
    #plugin-option, #upload-option {
        text-align: center;
        padding: 0 40px;
    }

    #upload-option {
        border-left: 1px solid rgba(0, 0, 0, 0.25);
    }

    #plugin-option {
        border-right: 1px solid rgba(0, 0, 0, 0.25);
    }

    #plugin-option p, #upload-option p {
        font-size: 14px;
    }

    #upload-option p {
        padding: 0 20px !important;
    }

    #plugin-option a, #upload-option a {
        color: rgb(128, 180, 160);
        font-weight: bold;
        text-decoration: none;
    }

    .plugin-logo {
        height: 100px;
        width: 100px;
        border-radius: 10px;
        padding: 5px;
        margin: 20px auto;
        background: rgb(238, 238, 238);
    }

    .plugin-logo img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    .option-btn {
        margin-top: 10px;
        background-color: #fa5555;
        color: #fff;
        font-weight: bold;
        border: 0px;
        margin-bottom: 20px
    }
</style>