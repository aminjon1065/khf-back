import { Link, usePage } from '@inertiajs/react';
import {
    Activity,
    Bell,
    Building,
    Building2,
    FileText,
    FolderTree,
    Image,
    Images,
    Inbox,
    Landmark,
    LayoutGrid,
    Mail,
    Map,
    MessagesSquare,
    Newspaper,
    PhoneCall,
    Settings,
    SquareStack,
    Tags,
    Target,
    Users,
    UsersRound,
} from 'lucide-react';
import { NavUser } from '@/components/nav-user';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarGroup,
    SidebarGroupLabel,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { useCurrentUrl } from '@/hooks/use-current-url';
import type { Auth } from '@/types';
import type { LucideIcon } from 'lucide-react';

type Item = { title: string; href: string; icon: LucideIcon; can?: string };
type Group = { label: string; items: Item[] };

const GROUPS: Group[] = [
    {
        label: 'Обзор',
        items: [{ title: 'Дашборд', href: '/admin', icon: LayoutGrid }],
    },
    {
        label: 'Контент',
        items: [
            { title: 'Новости', href: '/admin/news', icon: Newspaper, can: 'manage news' },
            { title: 'Документы', href: '/admin/documents', icon: FileText, can: 'manage documents' },
            { title: 'Категории документов', href: '/admin/document-categories', icon: FolderTree, can: 'manage documents' },
        ],
    },
    {
        label: 'Структура',
        items: [
            { title: 'Руководство', href: '/admin/leaders', icon: UsersRound, can: 'manage structure' },
            { title: 'Отделы', href: '/admin/departments', icon: Building2, can: 'manage structure' },
            { title: 'Представительства', href: '/admin/regional-offices', icon: Landmark, can: 'manage structure' },
        ],
    },
    {
        label: 'Деятельность',
        items: [
            { title: 'Направления', href: '/admin/directions', icon: Activity, can: 'manage activities' },
            { title: 'Программы', href: '/admin/programs', icon: Target, can: 'manage activities' },
        ],
    },
    {
        label: 'Форум',
        items: [
            { title: 'Категории', href: '/admin/forum-categories', icon: Tags, can: 'manage forum' },
            { title: 'Темы', href: '/admin/forum-topics', icon: MessagesSquare, can: 'manage forum' },
        ],
    },
    {
        label: 'Контакты и карта',
        items: [
            { title: 'Регионы', href: '/admin/regions', icon: Map, can: 'manage regions' },
            { title: 'Горячие линии', href: '/admin/hotlines', icon: PhoneCall, can: 'manage contacts' },
            { title: 'Офисы', href: '/admin/offices', icon: Building, can: 'manage contacts' },
        ],
    },
    {
        label: 'Главная',
        items: [
            { title: 'Слайды', href: '/admin/slides', icon: Images, can: 'manage home' },
            { title: 'Сервисы', href: '/admin/services', icon: SquareStack, can: 'manage home' },
        ],
    },
    {
        label: 'Обращения',
        items: [
            { title: 'Заявки о ЧС', href: '/admin/reports', icon: Inbox, can: 'manage submissions' },
            { title: 'Сообщения', href: '/admin/messages', icon: Mail, can: 'manage submissions' },
            { title: 'Подписки', href: '/admin/subscriptions', icon: Bell, can: 'manage submissions' },
        ],
    },
    {
        label: 'Система',
        items: [
            { title: 'Медиатека', href: '/admin/media', icon: Image, can: 'manage media' },
            { title: 'Настройки', href: '/admin/settings', icon: Settings, can: 'manage settings' },
            { title: 'Пользователи', href: '/admin/users', icon: Users, can: 'manage users' },
        ],
    },
];

export function AdminSidebar() {
    const { isCurrentUrl } = useCurrentUrl();
    const { auth } = usePage<{ auth: Auth }>().props;
    const permissions = auth.permissions ?? [];

    const can = (permission?: string) => !permission || permissions.includes(permission);

    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href="/admin" prefetch>
                                <span className="flex aspect-square size-8 items-center justify-center rounded-md bg-sidebar-primary text-sidebar-primary-foreground">
                                    <Newspaper className="size-4" />
                                </span>
                                <div className="grid flex-1 text-left text-sm leading-tight">
                                    <span className="truncate font-semibold">КҲФ · Админка</span>
                                    <span className="truncate text-xs text-muted-foreground">CMS</span>
                                </div>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                {GROUPS.map((group) => {
                    const items = group.items.filter((item) => can(item.can));
                    if (items.length === 0) {
                        return null;
                    }

                    return (
                        <SidebarGroup key={group.label} className="px-2 py-0">
                            <SidebarGroupLabel>{group.label}</SidebarGroupLabel>
                            <SidebarMenu>
                                {items.map((item) => (
                                    <SidebarMenuItem key={item.href}>
                                        <SidebarMenuButton
                                            asChild
                                            isActive={isCurrentUrl(item.href)}
                                            tooltip={{ children: item.title }}
                                        >
                                            <Link href={item.href} prefetch>
                                                <item.icon />
                                                <span>{item.title}</span>
                                            </Link>
                                        </SidebarMenuButton>
                                    </SidebarMenuItem>
                                ))}
                            </SidebarMenu>
                        </SidebarGroup>
                    );
                })}
            </SidebarContent>

            <SidebarFooter>
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
