import { usePage } from '@inertiajs/react';
import { useEffect } from 'react';
import { toast } from 'sonner';
import { AdminSidebar } from '@/components/admin/admin-sidebar';
import { AppContent } from '@/components/app-content';
import { AppShell } from '@/components/app-shell';
import { Separator } from '@/components/ui/separator';
import { SidebarTrigger } from '@/components/ui/sidebar';

type Flash = { success?: string; error?: string };

export default function AdminLayout({ children }: { children: React.ReactNode }) {
    const { flash } = usePage<{ flash?: Flash }>().props;

    useEffect(() => {
        if (flash?.success) {
            toast.success(flash.success);
        }
        if (flash?.error) {
            toast.error(flash.error);
        }
    }, [flash?.success, flash?.error]);

    return (
        <AppShell variant="sidebar">
            <AdminSidebar />
            <AppContent variant="sidebar" className="overflow-x-hidden">
                <header className="flex h-14 shrink-0 items-center gap-2 border-b px-4">
                    <SidebarTrigger className="-ml-1" />
                    <Separator orientation="vertical" className="mr-2 h-5" />
                    <span className="text-sm font-medium text-muted-foreground">Панель управления</span>
                </header>
                <div className="p-4 md:p-6">{children}</div>
            </AppContent>
        </AppShell>
    );
}
