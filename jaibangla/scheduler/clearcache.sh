#!/bin/bash
# Note:-"echo 3",it is not recommended in production instead use "echo 1"
# echo 1 for pagefile remove from cache memory
sync; echo 1 > /proc/sys/vm/drop_caches

