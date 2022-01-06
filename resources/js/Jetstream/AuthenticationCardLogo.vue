<template>
    <Link :href="'/'">
        <Logo />
    </Link>
</template>

<script>
    import { defineComponent } from 'vue'
    import { Link } from '@inertiajs/inertia-vue3';
    import Logo from './Logo';

    export default defineComponent({
        components: {
            Link,
            Logo,
        },
    })
</script>
