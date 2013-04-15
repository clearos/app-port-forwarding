
Name: app-port-forwarding
Epoch: 1
Version: 1.4.35
Release: 1%{dist}
Summary: Port Forwarding
License: GPLv3
Group: ClearOS/Apps
Source: %{name}-%{version}.tar.gz
Buildarch: noarch
Requires: %{name}-core = 1:%{version}-%{release}
Requires: app-base
Requires: app-network

%description
The Port Forwarding app makes it possible to allow access to systems on your local network from the Internet.

%package core
Summary: Port Forwarding - Core
License: LGPLv3
Group: ClearOS/Libraries
Requires: app-base-core
Requires: app-firewall-core >= 1:1.4.15
Requires: app-network-core

%description core
The Port Forwarding app makes it possible to allow access to systems on your local network from the Internet.

This package provides the core API and libraries.

%prep
%setup -q
%build

%install
mkdir -p -m 755 %{buildroot}/usr/clearos/apps/port_forwarding
cp -r * %{buildroot}/usr/clearos/apps/port_forwarding/


%post
logger -p local6.notice -t installer 'app-port-forwarding - installing'

%post core
logger -p local6.notice -t installer 'app-port-forwarding-core - installing'

if [ $1 -eq 1 ]; then
    [ -x /usr/clearos/apps/port_forwarding/deploy/install ] && /usr/clearos/apps/port_forwarding/deploy/install
fi

[ -x /usr/clearos/apps/port_forwarding/deploy/upgrade ] && /usr/clearos/apps/port_forwarding/deploy/upgrade

exit 0

%preun
if [ $1 -eq 0 ]; then
    logger -p local6.notice -t installer 'app-port-forwarding - uninstalling'
fi

%preun core
if [ $1 -eq 0 ]; then
    logger -p local6.notice -t installer 'app-port-forwarding-core - uninstalling'
    [ -x /usr/clearos/apps/port_forwarding/deploy/uninstall ] && /usr/clearos/apps/port_forwarding/deploy/uninstall
fi

exit 0

%files
%defattr(-,root,root)
/usr/clearos/apps/port_forwarding/controllers
/usr/clearos/apps/port_forwarding/htdocs
/usr/clearos/apps/port_forwarding/views

%files core
%defattr(-,root,root)
%exclude /usr/clearos/apps/port_forwarding/packaging
%exclude /usr/clearos/apps/port_forwarding/tests
%dir /usr/clearos/apps/port_forwarding
/usr/clearos/apps/port_forwarding/deploy
/usr/clearos/apps/port_forwarding/language
/usr/clearos/apps/port_forwarding/libraries
