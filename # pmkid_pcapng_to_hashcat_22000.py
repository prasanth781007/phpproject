python
>>> from scapy.all import get_if_list, conf
>>> print(conf.ifaces)          # should show interfaces without errors
>>> print(get_if_list())        # should list your network adapters


