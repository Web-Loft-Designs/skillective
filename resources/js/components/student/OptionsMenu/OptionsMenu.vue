<template>
    <div class="options-menu">
        <button @click.prevent="opened = !opened" :class="{
            'options-menu__button': true,
            'options-menu__button--opened': opened,
            'options-menu__button--squared': squared,
        }">Options</button>
        <transition name="scale">
            <ul v-if="opened && options.length" :class="{
                'options-menu__list': true,
                'options-menu__list--squared': squared,
            }">
                <li v-for="(option, index) in options" :key="index" :class="{
                    'options-menu__item': true,
                    'options-menu__item--red': option.red,
                }">
                    <button @click.prevent="emitMenuItemClick(index)">{{ option.text }}</button>
                </li>
            </ul>
        </transition>
    </div>
</template>

<script>
export default {
    name: "OptionsMenu",
    props: {
        options: {
            type: Array,
            default: () => {
                return [];
            },
        },
        squared: {
            type: Boolean,
            default: false,
        },
    },
    methods: {
        emitMenuItemClick(menuItemIndex) {
            this.$emit("menu-item-clicked", menuItemIndex);
        },
    },
    data() {
        return {
            opened: false,
        }
    },
}
</script>

<style lang="scss" scoped>
@import "./OptionsMenu.scss";
</style>