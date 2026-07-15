<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { CheckCircle2, PhoneCall, Sparkles, UsersRound } from 'lucide-vue-next';
import { computed } from 'vue';
import { toast } from 'vue-sonner';
import InputError from '@/components/InputError.vue';
import PageHeader from '@/components/PageHeader.vue';
import Pagination, { type PaginationLink } from '@/components/Pagination.vue';
import SelectInput from '@/components/SelectInput.vue';
import StatCard from '@/components/StatCard.vue';
import StatusBadge from '@/components/StatusBadge.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import type { Auth } from '@/types';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Leads',
                href: '/leads',
            },
        ],
    },
});

interface LeadRow {
    id: number;
    name: string;
    email: string | null;
    primaryPhone: string;
    status: string | null;
    source: string | null;
    assignedTo: string | null;
    createdAt: string | null;
}

interface LeadSourceOption {
    id: number;
    name: string;
    description: string | null;
}

interface TeamMemberOption {
    id: number;
    name: string;
}

const props = defineProps<{
    leads: {
        data: LeadRow[];
        links: PaginationLink[];
    };
    leadSources: LeadSourceOption[];
    teamMembers: TeamMemberOption[];
    leadStatusOptions: string[];
    stats: {
        total: number;
        new: number;
        contacted: number;
        qualified: number;
    };
}>();

const page = usePage<{ auth: Auth }>();
const permissions = computed(() => page.props.auth?.context?.permissions ?? []);
const canCreateLeads = computed(() => permissions.value.includes('leads.create'));

const leadForm = useForm({
    first_name: '',
    last_name: '',
    email: '',
    primary_phone: '',
    status: props.leadStatusOptions[0] ?? 'new',
    lead_source_id: '',
    assigned_to_user_id: '',
    notes: '',
});

const leadSourceForm = useForm({
    name: '',
    description: '',
});

const submitLead = () => {
    leadForm.transform((data) => ({
        ...data,
        lead_source_id: data.lead_source_id || null,
        assigned_to_user_id: data.assigned_to_user_id || null,
    })).post('/leads', {
        preserveScroll: true,
        onSuccess: () => {
            leadForm.reset('first_name', 'last_name', 'email', 'primary_phone', 'notes');
            toast.success('Lead created');
        },
    });
};

const submitLeadSource = () => {
    leadSourceForm.post('/lead-sources', {
        preserveScroll: true,
        onSuccess: () => {
            leadSourceForm.reset();
            toast.success('Lead source added');
        },
    });
};
</script>

<template>
    <Head title="Leads" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4">
        <PageHeader
            eyebrow="CRM"
            title="Leads"
            description="Capture leads, tag their source, and assign ownership inside the current workspace."
        />

        <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <StatCard label="Total leads" :value="stats.total" :icon="UsersRound" />
            <StatCard label="New" :value="stats.new" :icon="Sparkles" />
            <StatCard label="Contacted" :value="stats.contacted" :icon="PhoneCall" />
            <StatCard label="Qualified" :value="stats.qualified" :icon="CheckCircle2" />
        </section>

        <section class="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
            <div class="space-y-6">
                <form
                    v-if="canCreateLeads"
                    class="rounded-2xl border border-border bg-card p-6"
                    @submit.prevent="submitLead"
                >
                    <h2 class="text-lg font-semibold">Create lead</h2>
                    <p class="mt-1 text-sm text-muted-foreground">Add a lead and assign it to your team.</p>

                    <div class="mt-5 grid gap-5 md:grid-cols-2">
                        <div class="grid gap-2">
                            <Label for="lead-first-name">First name</Label>
                            <Input id="lead-first-name" v-model="leadForm.first_name" type="text" required :aria-invalid="Boolean(leadForm.errors.first_name)" />
                            <InputError :message="leadForm.errors.first_name" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="lead-last-name">Last name</Label>
                            <Input id="lead-last-name" v-model="leadForm.last_name" type="text" :aria-invalid="Boolean(leadForm.errors.last_name)" />
                            <InputError :message="leadForm.errors.last_name" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="lead-phone">Phone</Label>
                            <Input id="lead-phone" v-model="leadForm.primary_phone" type="text" required :aria-invalid="Boolean(leadForm.errors.primary_phone)" />
                            <InputError :message="leadForm.errors.primary_phone" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="lead-email">Email</Label>
                            <Input id="lead-email" v-model="leadForm.email" type="email" :aria-invalid="Boolean(leadForm.errors.email)" />
                            <InputError :message="leadForm.errors.email" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="lead-status">Status</Label>
                            <SelectInput id="lead-status" v-model="leadForm.status">
                                <option v-for="status in leadStatusOptions" :key="status" :value="status">{{ status }}</option>
                            </SelectInput>
                            <InputError :message="leadForm.errors.status" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="lead-source">Lead source</Label>
                            <SelectInput id="lead-source" v-model="leadForm.lead_source_id">
                                <option value="">None</option>
                                <option v-for="source in leadSources" :key="source.id" :value="String(source.id)">{{ source.name }}</option>
                            </SelectInput>
                            <InputError :message="leadForm.errors.lead_source_id" />
                        </div>
                        <div class="grid gap-2 md:col-span-2">
                            <Label for="lead-assignee">Assign to</Label>
                            <SelectInput id="lead-assignee" v-model="leadForm.assigned_to_user_id">
                                <option value="">Unassigned</option>
                                <option v-for="member in teamMembers" :key="member.id" :value="String(member.id)">{{ member.name }}</option>
                            </SelectInput>
                            <InputError :message="leadForm.errors.assigned_to_user_id" />
                        </div>
                        <div class="grid gap-2 md:col-span-2">
                            <Label for="lead-notes">Notes</Label>
                            <textarea
                                id="lead-notes"
                                v-model="leadForm.notes"
                                rows="4"
                                class="border-input dark:bg-input/30 focus-visible:border-ring focus-visible:ring-ring/50 w-full rounded-md border bg-transparent px-3 py-2 text-sm shadow-xs transition-[color,box-shadow] outline-none focus-visible:ring-[3px]"
                            ></textarea>
                            <InputError :message="leadForm.errors.notes" />
                        </div>
                    </div>

                    <div class="mt-5">
                        <Button type="submit" :disabled="leadForm.processing">
                            <Spinner v-if="leadForm.processing" />
                            Create lead
                        </Button>
                    </div>
                </form>

                <div v-else class="rounded-2xl border border-dashed border-border bg-card p-6 text-sm text-muted-foreground">
                    Your current role can view leads, but cannot create new leads. Contact a workspace admin for access.
                </div>

                <div class="rounded-2xl border border-border bg-card p-6">
                    <h2 class="text-lg font-semibold">Recent leads</h2>
                    <p class="mt-1 text-sm text-muted-foreground">Newest leads in the current tenant workspace.</p>

                    <!-- Desktop table -->
                    <div class="mt-5 hidden overflow-x-auto md:block">
                        <table class="min-w-full text-left text-sm">
                            <thead class="border-b border-border text-muted-foreground">
                                <tr>
                                    <th class="px-3 py-3 font-medium">Name</th>
                                    <th class="px-3 py-3 font-medium">Phone</th>
                                    <th class="px-3 py-3 font-medium">Status</th>
                                    <th class="px-3 py-3 font-medium">Source</th>
                                    <th class="px-3 py-3 font-medium">Assigned</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="lead in leads.data" :key="lead.id" class="border-b border-border/50 transition hover:bg-muted/50">
                                    <td class="px-3 py-3">
                                        <div class="font-medium">{{ lead.name }}</div>
                                        <div class="text-xs text-muted-foreground">{{ lead.email || 'No email' }}</div>
                                    </td>
                                    <td class="px-3 py-3">{{ lead.primaryPhone }}</td>
                                    <td class="px-3 py-3"><StatusBadge :status="lead.status" /></td>
                                    <td class="px-3 py-3">{{ lead.source || 'Direct' }}</td>
                                    <td class="px-3 py-3">{{ lead.assignedTo || 'Unassigned' }}</td>
                                </tr>
                                <tr v-if="leads.data.length === 0">
                                    <td colspan="5" class="px-3 py-10 text-center text-muted-foreground">
                                        No leads yet. Create the first one from the form above.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile cards -->
                    <div class="mt-5 space-y-3 md:hidden">
                        <div v-for="lead in leads.data" :key="lead.id" class="rounded-xl border border-border p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <div class="font-medium">{{ lead.name }}</div>
                                    <div class="mt-0.5 text-xs text-muted-foreground">{{ lead.email || 'No email' }}</div>
                                </div>
                                <StatusBadge :status="lead.status" />
                            </div>
                            <dl class="mt-3 grid grid-cols-2 gap-2 text-sm">
                                <div>
                                    <dt class="text-xs text-muted-foreground">Phone</dt>
                                    <dd>{{ lead.primaryPhone }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs text-muted-foreground">Source</dt>
                                    <dd>{{ lead.source || 'Direct' }}</dd>
                                </div>
                                <div class="col-span-2">
                                    <dt class="text-xs text-muted-foreground">Assigned</dt>
                                    <dd>{{ lead.assignedTo || 'Unassigned' }}</dd>
                                </div>
                            </dl>
                        </div>
                        <div v-if="leads.data.length === 0" class="rounded-xl border border-dashed border-border p-6 text-center text-sm text-muted-foreground">
                            No leads yet. Create the first one from the form above.
                        </div>
                    </div>

                    <Pagination :links="leads.links" />
                </div>
            </div>

            <div class="space-y-6">
                <form v-if="canCreateLeads" class="rounded-2xl border border-border bg-card p-6" @submit.prevent="submitLeadSource">
                    <h2 class="text-lg font-semibold">Add lead source</h2>
                    <p class="mt-1 text-sm text-muted-foreground">Track where leads are coming from.</p>

                    <div class="mt-5 grid gap-4">
                        <div class="grid gap-2">
                            <Label for="source-name">Source name</Label>
                            <Input id="source-name" v-model="leadSourceForm.name" type="text" required :aria-invalid="Boolean(leadSourceForm.errors.name)" />
                            <InputError :message="leadSourceForm.errors.name" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="source-description">Description</Label>
                            <textarea
                                id="source-description"
                                v-model="leadSourceForm.description"
                                rows="3"
                                class="border-input dark:bg-input/30 focus-visible:border-ring focus-visible:ring-ring/50 w-full rounded-md border bg-transparent px-3 py-2 text-sm shadow-xs transition-[color,box-shadow] outline-none focus-visible:ring-[3px]"
                            ></textarea>
                            <InputError :message="leadSourceForm.errors.description" />
                        </div>
                    </div>

                    <div class="mt-5">
                        <Button type="submit" variant="secondary" :disabled="leadSourceForm.processing">
                            <Spinner v-if="leadSourceForm.processing" />
                            Add source
                        </Button>
                    </div>
                </form>

                <div v-else class="rounded-2xl border border-dashed border-border bg-card p-6 text-sm text-muted-foreground">
                    Lead source setup is only available to roles that can create leads.
                </div>

                <div class="rounded-2xl border border-border bg-card p-6">
                    <h2 class="text-lg font-semibold">Lead sources</h2>
                    <p class="mt-1 text-sm text-muted-foreground">Available sources for this workspace.</p>

                    <div class="mt-5 space-y-3">
                        <div v-for="source in leadSources" :key="source.id" class="rounded-xl border border-border p-4">
                            <div class="font-medium">{{ source.name }}</div>
                            <div class="mt-1 text-sm text-muted-foreground">{{ source.description || 'No description yet.' }}</div>
                        </div>
                        <div v-if="leadSources.length === 0" class="rounded-xl border border-dashed border-border p-4 text-sm text-muted-foreground">
                            No lead sources yet. Add one to start tracking where leads come from.
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>
