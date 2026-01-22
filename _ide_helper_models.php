<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property int $tenant_id
 * @property int|null $user_id
 * @property int|null $plan_id
 * @property string $action
 * @property string|null $stripe_subscription_id
 * @property array<array-key, mixed>|null $metadata
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Plan|null $plan
 * @property-read \App\Models\Tenant $tenant
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BillingLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BillingLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BillingLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BillingLog whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BillingLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BillingLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BillingLog whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BillingLog wherePlanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BillingLog whereStripeSubscriptionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BillingLog whereTenantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BillingLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BillingLog whereUserId($value)
 */
	class BillingLog extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property int $price
 * @property int $is_active
 * @property int|null $max_members
 * @property int|null $max_projects
 * @property int $has_priority_support
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $billing_access
 * @property int $advanced_permissions
 * @property string|null $stripe_price_id
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant> $tenants
 * @property-read int|null $tenants_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereAdvancedPermissions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereBillingAccess($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereHasPrioritySupport($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereMaxMembers($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereMaxProjects($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereStripePriceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereUpdatedAt($value)
 */
	class Plan extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $tenant_id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Tenant $tenant
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereTenantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereUpdatedAt($value)
 */
	class Project extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $billable_type
 * @property int $billable_id
 * @property string $name
 * @property string $stripe_id
 * @property string $stripe_status
 * @property string|null $stripe_price
 * @property int|null $quantity
 * @property \Illuminate\Support\Carbon|null $trial_ends_at
 * @property \Illuminate\Support\Carbon|null $ends_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Cashier\SubscriptionItem> $items
 * @property-read int|null $items_count
 * @property-read \App\Models\User|null $owner
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription canceled()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription ended()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription expiredTrial()
 * @method static \Laravel\Cashier\Database\Factories\SubscriptionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription incomplete()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription notCanceled()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription notOnGracePeriod()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription notOnTrial()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription onGracePeriod()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription onTrial()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription pastDue()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription recurring()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereBillableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereBillableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereEndsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereStripeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereStripePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereStripeStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereTrialEndsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereUpdatedAt($value)
 */
	class Subscription extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $uuid
 * @property string $name
 * @property string $slug
 * @property array<array-key, mixed>|null $settings
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $plan_id
 * @property int|null $pending_plan_id
 * @property string|null $trial_ends_at
 * @property string|null $stripe_id
 * @property string|null $pm_type
 * @property string|null $pm_last_four
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TenantInvitation> $invitations
 * @property-read int|null $invitations_count
 * @property-read \App\Models\TenantOnboarding|null $onboarding
 * @property-read \App\Models\Plan|null $plan
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Project> $projects
 * @property-read int|null $projects_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Subscription> $subscriptions
 * @property-read int|null $subscriptions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant hasExpiredGenericTrial()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant onGenericTrial()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant wherePendingPlanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant wherePlanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant wherePmLastFour($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant wherePmType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereSettings($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereStripeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereTrialEndsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereUuid($value)
 */
	class Tenant extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $tenant_id
 * @property string $email
 * @property string $role
 * @property string $token
 * @property \Illuminate\Support\Carbon|null $accepted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Tenant $tenant
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantInvitation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantInvitation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantInvitation pending()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantInvitation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantInvitation whereAcceptedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantInvitation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantInvitation whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantInvitation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantInvitation whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantInvitation whereTenantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantInvitation whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantInvitation whereUpdatedAt($value)
 */
	class TenantInvitation extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $tenant_id
 * @property string $current_step
 * @property int $completed
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Tenant $tenant
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantOnboarding newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantOnboarding newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantOnboarding query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantOnboarding whereCompleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantOnboarding whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantOnboarding whereCurrentStep($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantOnboarding whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantOnboarding whereTenantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantOnboarding whereUpdatedAt($value)
 */
	class TenantOnboarding extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $stripe_id
 * @property string|null $pm_type
 * @property string|null $pm_last_four
 * @property string|null $trial_ends_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TenantInvitation> $pendingTenantInvitations
 * @property-read int|null $pending_tenant_invitations_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tenant> $tenants
 * @property-read int|null $tenants_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePmLastFour($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePmType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereStripeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTrialEndsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

