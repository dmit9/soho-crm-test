<script setup>
import {onMounted, ref,reactive } from 'vue';
import axios from 'axios';

const account = ref({name: '', website: '', phone: ''});
const deal = ref({owner: '',account_name: '',contact_name: '',campaign_source: '', deal_name: '', stage: ''});

const errors  = reactive({});
const accounts = ref([]);
const message = ref('');
let responseToken = ref({
    access_token: '',
    expires_at: ''
})
const owners = ref([])
const deals = ref([])
const contacts = ref([])
const campaigns = ref([])

const getAccount = async () => {
    try {
        const response = await axios.get('/get-accounts');
        owners.value = response.data
    } catch (error) {
        console.error(error);
    }
};
const createAccount = async () => {
    try {
        Object.keys(errors).forEach(key => delete errors[key]);
        message.value = "";
        const response = await axios.post('/create-account', {
            account_name: account.value.name,
            account_website: account.value.website,
            account_phone: account.value.phone
        });
        if ( response.status === 200) {
            message.value = 'account created!';
            account.name = "";
            account.website = "";
            account.phone = "";
            await getAccount();
        }
    } catch (error) {
        console.error(error);
        if (error.response && error.response.data.errors) {
            Object.assign(errors, error.response.data.errors);
        } else {
            message.value = "Error creating account";
        }
        return { account, errors, message, createAccount };
    }
};
const createDeal = async () => {
    try {
        const response = await axios.post('/create-deal', {
            owner: deal.value.owner,
            account_name: deal.value.account_name,
            contact_name: deal.value.contact_name,
            campaign_source: deal.value.campaign_source,
            deal_name: deal.value.deal_name,
            stage: deal.value.stage
        });
        if ( response.status === 200) {
            message.value = 'Deal created!';
            await getAccount();
        }
    } catch (error) {
        console.error(error);
        message.value = 'error ';
    }
};
const getValidToken = async () => {
    try {
        const response = await axios.get('/get-valid-token');
        responseToken.value = response.data;
        if ( response.status === 200) {
            message.value = 'Token Updated !';
            await getAccount();
        }
    } catch (error) {
        console.error(error);
    }
};

const getDeals = async () => {
    try {
        const response = await axios.get('/get-deals');
        deals.value = response.data
    } catch (error) {
        console.error(error);
    }
};
const getContacts = async () => {
    try {
        const response = await axios.get('/get-contacts');
        contacts.value = response.data
    } catch (error) {
        console.error(error);
    }
};
const getCampaigns = async () => {
    try {
        const response = await axios.get('/get-campaigns');
        campaigns.value = response.data
    } catch (error) {
        console.error(error);
    }
};


onMounted(() => {
    getValidToken()
    getAccount();
    getDeals();
    getContacts();
    getCampaigns();
});

</script>

<template>
    <div class="flex flex-col items-center gap-2 border mt-6 mb-8 text-xl">

        <h2 v-if="message" class="bg-green-100">{{ message }}</h2>
        <div class="flex flex-col  items-center gap-4">
            <div class="flex"><h2>Actual Token :</h2>
                <p>{{ responseToken.access_token }}</p>
            </div>
            <p>Token expires at {{ responseToken.expires_at }}</p>
            <button class="bg-gray-100" @click="getValidToken">getValidToken</button>
        </div>
        <div class="flex pl-10 pr-10 mt-4  items-center gap-4 border  w-full">
            <h2>Create account</h2>
            <div class="w-full">
                <input v-model="account.name" placeholder="Account Name" class="bg-gray-50 border rounded-lg"/>
                <p v-if="errors.account_name" class="text-red-500 text-xs mt-1">{{ errors.account_name[0] }}</p>
            </div>

            <div class="w-full">
                <input v-model="account.website" placeholder="Website" class="bg-gray-50 border rounded-lg "/>
                <p v-if="errors.account_website" class="text-red-500 text-xs mt-1">{{ errors.account_website[0] }}</p>
            </div>

            <div class="w-full">
                <input v-model="account.phone" placeholder="Phone" class="bg-gray-50 border rounded-lg "/>
                <p v-if="errors.account_phone" class="text-red-500 text-xs mt-1">{{ errors.account_phone[0] }}</p>
            </div>
            <button class="bg-gray-100" @click="createAccount">Add account</button>
        </div>
        <div class="flex  flex-col  mt-4 w-full  items-center border rounded-lg">
            <h2>Create Deal</h2>
            <div class="border "><label>Deal_Name:</label>
                <select v-model="deal.deal_name">
                    <option  v-for="deal in deals" :key="deal.id" :value="deal.Stage">{{ deal.Deal_Name }} </option>
                </select>
            </div>
            <div class="border "><label>Stage:</label>
                <select v-model="deal.stage">
                    <option  v-for="deal in deals" :key="deal.id" :value="deal.Stage">{{ deal.Stage }} </option>
                </select>
            </div>
            <div class="border "><label>Owner id:</label>
                <select v-model="deal.owner">
                    <option  v-for="owner in owners" :key="owner.id" :value="owner.Owner.id">{{ owner.Owner?.id }} </option>
                </select>
            </div>
            <div class="border"><label>Account_Name:</label>
                <select v-model="deal.account_name">
                    <option v-for="owner in owners" :key="owner.id" :value="owner.id">{{ owner.id }}</option>
                </select>
            </div>
            <div class="border"><label>Contact_Name:</label>
                <select v-model="deal.contact_name">
                    <option v-for="contact in contacts" :key="contact.id" :value="contact.id">{{ contact.id }}</option>
                </select>
            </div>
            <div class="border"><label>Campaign_Source:</label>
                <select v-model="deal.campaign_source">
                    <option v-for="campaign in campaigns" :key="campaign.id" :value="campaign.id">{{ campaign.id }}</option>
                </select>
            </div>
            <button @click="createDeal" class="bg-gray-100">Add Deal</button>

        </div>

        <div class="flex flex-col w-full  items-center border">
            <h2>Accounts  <button class="bg-gray-100" @click="getAccount">getAccount</button></h2>
            <ul v-if="owners.length">
                <li v-for="owner in owners" :key="owner.id" class="border mt-4">
                    <div><strong>Account_Name id: {{ owner.id }} </strong></div>
                    <div>Owner: {{ owner.Owner?.name }}, {{ owner.Owner?.email }} , {{ owner.Owner?.id }}</div>
                    <div>Website: {{ owner.Website }}, Account_Number: {{ owner.Account_Number }}, Phone: {{ owner.Phone }}, Account_Name: {{ owner.Account_Name }}</div>
                    <div>$layout_id: {{ owner.$layout_id }} </div>

                </li>
            </ul>
        </div>

        <div class="flex flex-col w-full  items-center  border">
            <h2>Deals  <button class="bg-gray-100" @click="getDeals">getDeals</button></h2>
            <ul v-if="deals.length">
                <li v-for="deal in deals" :key="deal.id" class="border mt-4">
                    <div><strong>id: {{ deal.id }}</strong> </div>
                    <div>Owner: {{ deal.Owner?.name }}, {{ deal.Owner?.email }} , {{ deal.Owner?.id }}</div>
                    <div>Stage: {{ deal.Stage }} </div>
                    <div>Deal_Name: {{ deal.Deal_Name }} </div>
                    <div>Account_Name: {{ deal.Account_Name }} </div>
                    <div>Contact_Name: {{ deal.Contact_Name }} </div>
                </li>
            </ul>
        </div>

        <div class="flex flex-col w-full  items-center border  mb-2">
            <h2>Contacts  <button class="bg-gray-100" @click="getContacts">getContacts</button></h2>
            <ul v-if="contacts.length">
                <li v-for="contact in contacts" :key="contact.id" class="border mt-4">
                    <div><strong>Contact_Name id: {{ contact.id }} </strong></div>
                    <div>Owner: {{ contact.Owner?.name }}, {{ contact.Owner?.email }} , {{ contact.Owner?.id }}</div>
                </li>
            </ul>
        </div>
        <div class="flex flex-col w-full  items-center  border  mb-8">
            <h2>Campaigns  <button  class="bg-gray-100" @click="getCampaigns">getCampaigns</button></h2>
            <ul v-if="campaigns.length">
                <li v-for="campaign in campaigns" :key="campaign.id" class="border mt-4">
                    <div><strong>Campaign_Source id: {{ campaign.id }} </strong></div>
                    <div>Owner: {{ campaign.Owner?.name }}, {{ campaign.Owner?.email }} , {{ campaign.Owner?.id }}</div>
                </li>
            </ul>
        </div>
    </div>
</template>
