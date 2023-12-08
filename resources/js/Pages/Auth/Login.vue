<script setup>
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import SocialButton from '@/Components/Auth/SocialButton.vue'

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.transform(data => ({
        ...data,
        remember: form.remember ? 'on' : '',
    })).post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};

const socialLogin = (type) => {
    switch(type){
        case "oauth":
            router.get(route('oauth-login'));
            break;
        case "kakao":
            router.get(route('kakao-login'));
            break;
        case "naver":
            break;
        case "facebook":
        case "google":
        case "github":
            router.get(route('social-login', { provider: type }));
            break;
        case "apple":
            break;
    }


}
</script>

<template>
    <Head title="Log in" />

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <template #social>
            <div class="text-center">
                <SocialButton
                    @click="socialLogin('oauth')"
                    :title="'OAuth 시작하기'"
                    :color="'yellow'"
                />

                <SocialButton
                    @click="socialLogin('kakao')"
                    :title="'카카오로 시작하기'"
                    :color="'yellow'"
                />

                <SocialButton
                    @click="socialLogin('naver')"
                    :title="'네이버로 시작하기'"
                    :color="'green'"
                />

                <SocialButton
                    @click="socialLogin('facebook')"
                    :title="'페이스북로 시작하기'"
                    :color="'default'"
                />

                <SocialButton
                    @click="socialLogin('google')"
                    :title="'구글로 시작하기'"
                    :color="'red'"
                />

                <SocialButton
                    @click="socialLogin('apple')"
                    :title="'애플로 시작하기'"
                    :color="'dark'"
                />

                <SocialButton
                    @click="socialLogin('github')"
                    :title="'github 시작하기'"
                    :color="'dark'"
                />
            </div>
        </template>


        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="이메일 입력" />
                <TextInput
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="mt-1 block w-full"
                    required
                    autofocus
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="비밀번호 입력" />
                <TextInput
                    id="password"
                    v-model="form.password"
                    type="password"
                    class="mt-1 block w-full"
                    required
                    autocomplete="current-password"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="block mt-4">
                <label class="flex items-center">
                    <Checkbox v-model:checked="form.remember" name="remember" />
                    <span class="ms-2 text-sm text-gray-600">아이디 저장</span>
                </label>
            </div>

            <div class="flex items-center justify-end mt-4">
                <Link v-if="canResetPassword" :href="route('password.request')" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    비밀번호를 잊으셨어요?
                </Link>

                <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    로그인하기
                </PrimaryButton>
            </div>
        </form>
    </AuthenticationCard>
</template>
