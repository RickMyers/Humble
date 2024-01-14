{
    "cpu": {
        "load": "{$monitor->getServerLoad()}"
    },
    "memory": {
        "total": "{$monitor->getTotalMemory()}",
        "free": "{$monitor->getFreeMemory()}",
        "used": "{$monitor->getUsedMemory()}",
        "percentage": "{$monitor->getPercentMemory()}"    
    },
    "apache": {
        "thread_count": "{$monitor->getApacheThreads()}"
    },
    "tasks": {
        "count": "{$monitor->getTotalThreads()}"
    },
    "uptime": {
        "duration": ""
    }
}
