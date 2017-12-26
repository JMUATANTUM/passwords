<template>
    <div class="background" id="passwords-create-new">
        <div class="window">
            <div class="title nc-theming-main-background nc-theming-contrast">
                <translate>Create a new password</translate>
                <i class="fa fa-times close" @click="closeWindow()"></i>
            </div>
            <form class="content" v-on:submit.prevent="submitAction($event)">
                <div class="form">
                    <translate tag="div" class="section-title">General</translate>
                    <div class="form-grid">
                        <translate tag="label" for="password-label">Name</translate>
                        <input id="password-label" type="text" name="label" maxlength="48" value="">
                        <translate tag="label" for="password-username">Username</translate>
                        <input id="password-username" type="text" name="username" maxlength="48" value="" required>
                        <translate tag="label" for="password-password">Password</translate>
                        <div class="password-field">
                            <div class="icons">
                                <i class="fa" :class="{ 'fa-eye': showPassword, 'fa-eye-slash': !showPassword }" @click="togglePasswordVisibility()" title="Toggle visibility"></i>
                                <i class="fa fa-refresh" :class="{ 'fa-spin': showLoader }" @click="generateRandomPassword()" title="Generate random password"></i>
                            </div>
                            <input id="password-password" :type="showPassword ? 'text':'password'" name="password" maxlength="48" :value="password" required>
                        </div>
                        <translate tag="label" for="password-url">Website</translate>
                        <input id="password-url" type="text" name="url" maxlength="2048" value="">
                        <!-- <passwords-tags></passwords-tags> -->
                        <foldout title="More Options">
                            <div class="form-grid">
                                <translate tag="label" for="password-favourite">Favourite</translate>
                                <input id="password-favourite" name="favourite" type="checkbox" value="1">
                                <translate tag="label" for="password-sse">Encryption</translate>
                                <select id="password-sse" name="sse" disabled title="There is only one option right now">
                                    <translate tag="option" value="SSEv1r1" title="Use Simple Server Side Encryption V1" selected>SSE V1</translate>
                                </select>
                            </div>
                        </foldout>
                    </div>
                </div>
                <div class="notes">
                    <translate tag="label" for="password-notes">Notes</translate>
                    <textarea id="password-notes" name="notes" maxlength="4096"></textarea>
                </div>
                <div class="controls">
                    <input class="nc-theming-main-background nc-theming-contrast" type="submit" value="Save">
                </div>
            </form>
        </div>
    </div>
</template>

<script>
    import SimpleMDE from 'simplemde';
    import Translate from '@vc/Translate.vue';
    import Foldout from '@vc/Foldout.vue';
    import Messages from '@js/Classes/Messages';
    import Events from '@js/Classes/Events';
    import Utility from '@js/Classes/Utility';
    import ThemeManager from '@js/Manager/ThemeManager';
    import API from "@js/Helper/api";
    import $ from "jquery";

    export default {
        data() {
            return {
                showPassword: false,
                showLoader: false,
                simplemde   : null,
                password   : null,
                folder   : null,
            }
        },

        components: {
            Foldout,
            Translate
        },

        mounted() {
            this.simplemde = new SimpleMDE(
                {
                    element                : document.getElementById('password-notes'),
                    hideIcons              : ['fullscreen', 'side-by-side'],
                    autoDownloadFontAwesome: false,
                    spellChecker           : false,
                    placeholder            : Utility.translate('Take some notes'),
                    status                 : false
                });
            ThemeManager.setBorderColor('#passwords-create-new .section-title, #passwords-create-new .notes label');
        },

        methods: {
            closeWindow             : function () {
                this.$destroy();
                let $container = $('#app-popup');
                $container.find('div').remove();
                $container.html('<div></div>');
            },
            togglePasswordVisibility: function () {
                this.showPassword = !this.showPassword;
            },
            generateRandomPassword  : function () {
                this.showLoader = true;

                API.generatePassword()
                    .then((d) => {
                        this.password = d.password;
                        this.showLoader = false;
                        this.showPassword = true;
                    })
                    .catch(() => {
                        this.showLoader = false;
                    });
            },
            submitAction    : function ($event) {
                let $element = $($event.target);
                let $data = $element.serializeArray();
                let password = {};

                for (let i = 0; i < $data.length; i++) {
                    let entry = $data[i];
                    password[entry.name] = entry.value;
                }
                password.notes = this.simplemde.value();

                if(this.folder) password.folder = this.folder;

                API.createPassword(password)
                    .then((data) => {
                        this.closeWindow();
                        Messages.notification('Password created');
                        password.type = 'password';
                        password.id = data.id;
                        password.revision = data.revision;
                        password.created = new Date();
                        password.updated = password.created;
                        Events.fire('password.created', password);
                    })
                    .catch(() => {
                        Messages.alert(e.message, 'Creating Password Failed');
                    });
            }
        }
    };
</script>

<style lang="scss">
    @import "~simplemde/dist/simplemde.min.css";

    #app-popup {
        .background {
            position         : fixed;
            top              : 0;
            left             : 0;
            width            : 100%;
            height           : 100%;
            background-color : rgba(0, 0, 0, 0.7);
            z-index          : 3001;

            .window {
                position              : fixed;
                top                   : 6%;
                left                  : 15%;
                width                 : 70%;
                height                : 88%;
                z-index               : 9999;
                overflow              : hidden;
                background-color      : $color-white;
                border-radius         : 3px;
                box-sizing            : border-box;
                display               : grid;
                grid-template-columns : 100%;
                grid-template-areas   : "title" "content";
                grid-template-rows    : 3.25rem auto;
                justify-items         : stretch;
                align-items           : stretch;

                .title {
                    color: $color-contrast;
                    background-color: $color-theme;
                    grid-area : title;
                    padding   : 1rem;
                    font-size : 1.25rem;

                    .close {
                        float  : right;
                        cursor : pointer;
                    }
                }

                .content {
                    grid-area : content;
                    overflow  : auto;
                }
            }
        }

        #passwords-create-new {
            .content {
                display               : grid;
                grid-template-columns : 1fr 1fr;
                grid-template-rows    : 9fr 1fr;
                grid-template-areas   : "form notes" "controls notes";
                grid-column-gap       : 15px;
                padding               : 15px;

                .form {
                    grid-area : form;

                    .form-grid {
                        display               : grid;
                        grid-template-columns : auto 3fr;
                        grid-template-rows    : 1fr;
                        grid-row-gap          : 5px;
                        justify-items         : left;
                        align-items           : end;

                        .tags-container,
                        .foldout-container {
                            grid-column  : 1 / span 2;
                            justify-self : stretch;
                        }

                        label {
                            padding : 0 0.9rem 5px 0;
                            cursor : pointer;
                        }
                    }

                    .section-title {
                        font-size     : 1.1rem;
                        padding       : 0 0 0.25rem 0;
                        border-bottom : 1px solid $color-grey-light;
                    }

                    .password-field {
                        display   : block;
                        width     : 100%;
                        max-width : 275px;
                        position  : relative;

                        input {
                            max-width : initial;
                        }

                        .icons {
                            position    : absolute;
                            top         : 0;
                            right       : 3px;
                            bottom      : 0;
                            display     : flex;
                            align-items : center;

                            i.fa {
                                font-size : 1rem;
                                cursor    : pointer;
                                margin    : 3px;
                            }
                        }
                    }

                    label {
                        display   : block;
                        font-size : 0.9rem;
                    }

                    input[type=text],
                    input[type=password] {
                        cursor    : text;
                        width     : 100%;
                        max-width : 275px;
                    }

                    select {
                        width     : 100%;
                        max-width : 275px;
                    }
                }

                .notes {
                    grid-area : notes;

                    label {
                        font-size     : 1.1rem;
                        padding       : 0 0 0.25rem 0;
                        border-bottom : 1px solid $color-grey-light;
                        display       : block;
                        margin-bottom : 0.25rem;
                        cursor        : default;
                    }
                }

                .controls {
                    grid-area  : controls;
                    align-self : end;

                    input {
                        width     : 100%;
                        font-size : 1.1rem;
                    }
                }
            }
        }
    }

</style>