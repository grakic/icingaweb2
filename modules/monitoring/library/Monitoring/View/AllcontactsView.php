<?php

namespace Icinga\Module\Monitoring\View;

class AllcontactsView extends AbstractView
{
    protected $query;

    protected $availableColumns = array(
        'host_name',
        'contact_name',
        'contact_alias',
        'contact_email',
        'contact_pager',
        'contact_notify_hosts',
        'contact_notify_services',
        'contact_has_host_notfications',
        'contact_has_service_notfications',
        'contact_can_submit_commands',
        'contact_notify_service_recovery',
        'contact_notify_service_warning',
        'contact_notify_service_critical',
        'contact_notify_service_unknown',
        'contact_notify_service_flapping',
        'contact_notify_service_downtime',
        'contact_notify_host_recovery',
        'contact_notify_host_down',
        'contact_notify_host_unreachable',
        'contact_notify_host_flapping',
        'contact_notify_host_downtime',
        'host_object_id',
        'host_name',
        'service_object_id',
        'service_host_name',
        'service_description',
/*        'contact_alias',
        'contact_email',
        'contact_pager',
        'contact_notify_hosts',
        'contact_notify_services',
*/
    );

/*
    protected $sortDefaults = array(
        'contact_alias' => array(
            'default_dir' => self::SORT_ASC
        )
    );*/
}