<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { Building2, CircleDashed, Target, UserPlus, UsersRound } from 'lucide-vue-next';
import { computed } from 'vue';
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
    leads: LeadRow[];
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
    leadForm
        .transform((data) => ({
            ...data,
            lead_source_id: data.lead_source_id || null,
            assigned_to_user_id: data.assigned_to_user_id || null,
        }))
        .post('/leads', {
            preserveScroll: true,
            onSuccess: () =>
                leadForm.reset(
                    'first_name',
                    'last_name',
                    'email',
                    'primary_phone',
                    'notes',
                ),
        });
};

const submitLeadSource = () => {
    leadSourceForm.post('/lead-sources', {
        preserveScroll: true,
        onSuccess: () => leadSourceForm.reset(),
    });
};

const statusClass = (status: string | null) => {
    switch ((status ?? '').toLowerCase()) {
        case 'qualified':
            return 'crm-chip crm-chip-success';
        case 'contacted':
            return 'crm-chip crm-chip-warning';
        default:
            return 'crm-chip crm-chip-muted';
    }
};
</script>

<template>
    <Head title="Leads" />

    <div class="crm-page">
        <section class="crm-hero">
            <div class="flex flex-col gap-6 xl:flex-row xl:items-start xl:justify-between">
                <div>
                    <p class="crm-eyebrow">CRM pipeline</p>
                    <h1 class="crm-title">Lead management</h1>
                    <p class="crm-subtitle">
                        Capture fresh inquiries, track source quality, and assign ownership fast enough that no lead goes cold.
                    </p>

                    <div class="mt-5 flex flex-wrap gap-2">
                        <span class="crm-chip crm-chip-success">{{ props.stats.total }} total leads</span>
                        <span class="crm-chip crm-chip-muted">{{ props.leadSources.length }} lead sources</span>
                        <span class="crm-chip crm-chip-muted">{{ props.teamMembers.length }} assignable teammates</span>
                    </div>
                </div>

                <div class="crm-card-soft max-w-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-muted-foreground">Pipeline intent</p>
                    <p class="mt-3 text-sm leading-6 text-muted-foreground">
                        A professional sales panel should make lead intake feel structured, not chaotic. This layout is built for that.
                    </p>
                </div>
            </div>
        </section>

        <section class="crm-stat-grid">
            <article class="crm-stat-card">
                <div class="crm-stat-icon"><UsersRound class="size-5" /></div>
                <p class="crm-stat-label">Total leads</p>
                <p class="crm-stat-value">{{ props.stats.total }}</p>
                <p class="crm-stat-caption">Every lead record stored inside this workspace.</p>
            </article>
            <article class="crm-stat-card">
                <div class="crm-stat-icon"><CircleDashed class="size-5" /></div>
                <p class="crm-stat-label">New</p>
                <p class="crm-stat-value">{{ props.stats.new }}</p>
                <p class="crm-stat-caption">Fresh entries that still need first-touch handling.</p>
            </article>
            <article class="crm-stat-card">
                <div class="crm-stat-icon"><Target class="size-5" /></div>
                <p class="crm-stat-label">Contacted</p>
                <p class="crm-stat-value">{{ props.stats.contacted }}</p>
                <p class="crm-stat-caption">Prospects where the team has already opened communication.</p>
            </article>
            <article class="crm-stat-card">
                <div class="crm-stat-icon"><UserPlus class="size-5" /></div>
                <p class="crm-stat-label">Qualified</p>
                <p class="crm-stat-value">{{ props.stats.qualified }}</p>
                <p class="crm-stat-caption">Higher-intent leads ready for more serious follow-up.</p>
            </article>
        </section>

        <section class="grid gap-6 xl:grid-cols-[1.1fr_0.9fr]">
            <div class="space-y-6">
                <form v-if="canCreateLeads" class="crm-card" @submit.prevent="submitLead">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <h2 class="crm-panel-title">Create lead</h2>
                            <p class="crm-panel-copy">Add a clean lead record and assign it to the right teammate immediately.</p>
                        </div>
                    </div>

                    <div class="mt-5 grid gap-5 md:grid-cols-2">
                        <div>
                            <label class="crm-label">First name</label>
                            <input v-model="leadForm.first_name" type="text" class="crm-input" />
                            <p v-if="leadForm.errors.first_name" class="crm-error">{{ leadForm.errors.first_name }}</p>
                        </div>
                        <div>
                            <label class="crm-label">Last name</label>
                            <input v-model="leadForm.last_name" type="text" class="crm-input" />
                            <p v-if="leadForm.errors.last_name" class="crm-error">{{ leadForm.errors.last_name }}</p>
                        </div>
                        <div>
                            <label class="crm-label">Phone</label>
                            <input v-model="leadForm.primary_phone" type="text" class="crm-input" />
                            <p v-if="leadForm.errors.primary_phone" class="crm-error">{{ leadForm.errors.primary_phone }}</p>
                        </div>
                        <div>
                            <label class="crm-label">Email</label>
                            <input v-model="leadForm.email" type="email" class="crm-input" />
                            <p v-if="leadForm.errors.email" class="crm-error">{{ leadForm.errors.email }}</p>
                        </div>
                        <div>
                            <label class="crm-label">Status</label>
                            <select v-model="leadForm.status" class="crm-select">
                                <option v-for="status in props.leadStatusOptions" :key="status" :value="status">
                                    {{ status }}
                                </option>
                            </select>
                            <p v-if="leadForm.errors.status" class="crm-error">{{ leadForm.errors.status }}</p>
                        </div>
                        <div>
                            <label class="crm-label">Lead source</label>
                            <select v-model="leadForm.lead_source_id" class="crm-select">
                                <option value="">None</option>
                                <option v-for="source in props.leadSources" :key="source.id" :value="String(source.id)">
                                    {{ source.name }}
                                </option>
                            </select>
                            <p v-if="leadForm.errors.lead_source_id" class="crm-error">{{ leadForm.errors.lead_source_id }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="crm-label">Assign to</label>
                            <select v-model="leadForm.assigned_to_user_id" class="crm-select">
                                <option value="">Unassigned</option>
                                <option v-for="member in props.teamMembers" :key="member.id" :value="String(member.id)">
                                    {{ member.name }}
                                </option>
                            </select>
                            <p v-if="leadForm.errors.assigned_to_user_id" class="crm-error">
                                {{ leadForm.errors.assigned_to_user_id }}
                            </p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="crm-label">Notes</label>
                            <textarea v-model="leadForm.notes" rows="4" class="crm-textarea"></textarea>
                            <p v-if="leadForm.errors.notes" class="crm-error">{{ leadForm.errors.notes }}</p>
                        </div>
                    </div>

                    <div class="mt-5">
                        <button type="submit" :disabled="leadForm.processing" class="crm-button-primary">
                            {{ leadForm.processing ? 'Saving...' : 'Create lead' }}
                        </button>
                    </div>
                </form>

                <div v-else class="crm-empty-state">
                    Your current role can review leads, but cannot create new ones.
                </div>

                <article class="crm-card">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <h2 class="crm-panel-title">Recent leads</h2>
                            <p class="crm-panel-copy">Newest records in the current workspace.</p>
                        </div>
                    </div>

                    <div class="crm-table-wrap">
                        <table class="crm-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Status</th>
                                    <th>Source</th>
                                    <th>Assigned</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="lead in props.leads" :key="lead.id">
                                    <td>
                                        <div class="font-medium text-foreground">{{ lead.name }}</div>
                                        <div class="mt-1 text-xs text-muted-foreground">{{ lead.email || 'No email' }}</div>
                                    </td>
                                    <td>{{ lead.primaryPhone }}</td>
                                    <td>
                                        <span :class="statusClass(lead.status)">
                                            {{ lead.status || 'new' }}
                                        </span>
                                    </td>
                                    <td>{{ lead.source || 'Direct' }}</td>
                                    <td>{{ lead.assignedTo || 'Unassigned' }}</td>
                                </tr>
                                <tr v-if="props.leads.length === 0">
                                    <td colspan="5">
                                        <div class="crm-empty-state">No leads yet. Create the first one from the form above.</div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </article>
            </div>

            <div class="space-y-6">
                <form v-if="canCreateLeads" class="crm-card" @submit.prevent="submitLeadSource">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <h2 class="crm-panel-title">Lead sources</h2>
                            <p class="crm-panel-copy">Keep source tracking consistent so acquisition quality stays visible.</p>
                        </div>
                        <Building2 class="size-5 text-orange-500" />
                    </div>

                    <div class="mt-5 grid gap-4">
                        <div>
                            <label class="crm-label">Source name</label>
                            <input v-model="leadSourceForm.name" type="text" class="crm-input" />
                            <p v-if="leadSourceForm.errors.name" class="crm-error">{{ leadSourceForm.errors.name }}</p>
                        </div>
                        <div>
                            <label class="crm-label">Description</label>
                            <textarea v-model="leadSourceForm.description" rows="3" class="crm-textarea"></textarea>
                            <p v-if="leadSourceForm.errors.description" class="crm-error">
                                {{ leadSourceForm.errors.description }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-5">
                        <button type="submit" :disabled="leadSourceForm.processing" class="crm-button-secondary">
                            {{ leadSourceForm.processing ? 'Saving...' : 'Add source' }}
                        </button>
                    </div>
                </form>

                <div v-else class="crm-empty-state">
                    Lead source setup is only available to roles that can create leads.
                </div>

                <article class="crm-card">
                    <h2 class="crm-panel-title">Available sources</h2>
                    <p class="crm-panel-copy">Use these consistently so reports and attribution stay reliable.</p>

                    <div class="mt-5 space-y-3">
                        <div v-for="source in props.leadSources" :key="source.id" class="crm-card-soft">
                            <div class="font-medium text-foreground">{{ source.name }}</div>
                            <div class="mt-1 text-sm text-muted-foreground">
                                {{ source.description || 'No description yet.' }}
                            </div>
                        </div>
                        <div v-if="props.leadSources.length === 0" class="crm-empty-state">
                            No lead sources configured yet.
                        </div>
                    </div>
                </article>
            </div>
        </section>
    </div>
</template>
