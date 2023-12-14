{
    "cpu": {
        "load": "{$monitor->getServerLoad()}"
    },
    "memory": {
        "total": "{$monitor->getMemoryTotal()}",
        "free": "{$monitor->getMemoryFree()}",
        "used": "{$monitor->getMemoryUsed()}",
        "percentage": "{$monitor->getMemoryPercentage()}"    
    },
    "apache": {
        "thread_count": "{$monitor->getThreadCount()}"
    },
    "tasks": {
        "count": "{$monitor->getTaskCount()}"
    },
    "uptime": {
        "duration": ""
    }
}
