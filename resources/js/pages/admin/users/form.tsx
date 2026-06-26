import { Head, Link, useForm } from '@inertiajs/react';
import { ArrowLeft, Save } from 'lucide-react';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';

type UserData = {
    id: number;
    name: string;
    email: string;
    role: string | null;
};

type Props = {
    roles: string[];
    item?: UserData;
};

const roleLabels: Record<string, string> = {
    admin: 'Администратор',
    editor: 'Редактор',
};

export default function UserForm({ roles, item }: Props) {
    const isEdit = Boolean(item);

    const form = useForm({
        name: item?.name ?? '',
        email: item?.email ?? '',
        password: '',
        role: item?.role ?? 'editor',
    });

    const errors = form.errors as Record<string, string>;

    const submit = (event: React.FormEvent) => {
        event.preventDefault();
        if (isEdit && item) {
            form.transform((data) => ({ ...data, _method: 'put' }));
            form.post(`/admin/users/${item.id}`);
        } else {
            form.transform((data) => data);
            form.post('/admin/users');
        }
    };

    return (
        <>
            <Head title={isEdit ? 'Редактирование пользователя' : 'Новый пользователь'} />

            <form onSubmit={submit}>
                <div className="flex items-center justify-between gap-4">
                    <div className="flex items-center gap-3">
                        <Button asChild variant="ghost" size="icon">
                            <Link href="/admin/users" aria-label="Назад">
                                <ArrowLeft className="size-4" />
                            </Link>
                        </Button>
                        <h1 className="text-2xl font-bold tracking-tight">
                            {isEdit ? 'Редактирование пользователя' : 'Новый пользователь'}
                        </h1>
                    </div>
                    <Button type="submit" disabled={form.processing}>
                        <Save className="size-4" /> Сохранить
                    </Button>
                </div>

                <div className="mt-6 max-w-2xl space-y-5">
                    <div className="grid gap-2">
                        <Label>
                            Имя <span className="text-destructive">*</span>
                        </Label>
                        <Input
                            value={form.data.name}
                            onChange={(event) => form.setData('name', event.target.value)}
                            placeholder="Иван Иванов"
                        />
                        <InputError message={errors.name} />
                    </div>

                    <div className="grid gap-2">
                        <Label>
                            Email <span className="text-destructive">*</span>
                        </Label>
                        <Input
                            type="email"
                            value={form.data.email}
                            onChange={(event) => form.setData('email', event.target.value)}
                            placeholder="user@example.com"
                        />
                        <InputError message={errors.email} />
                    </div>

                    <div className="grid gap-2">
                        <Label>
                            Пароль {!isEdit && <span className="text-destructive">*</span>}
                            {isEdit && (
                                <span className="ml-1 text-xs font-normal text-muted-foreground">
                                    оставьте пустым, чтобы не менять
                                </span>
                            )}
                        </Label>
                        <Input
                            type="password"
                            value={form.data.password}
                            onChange={(event) => form.setData('password', event.target.value)}
                            placeholder="••••••••"
                            autoComplete="new-password"
                        />
                        <InputError message={errors.password} />
                    </div>

                    <div className="grid gap-2">
                        <Label>Роль</Label>
                        <Select value={form.data.role} onValueChange={(value) => form.setData('role', value)}>
                            <SelectTrigger>
                                <SelectValue />
                            </SelectTrigger>
                            <SelectContent>
                                {roles.map((role) => (
                                    <SelectItem key={role} value={role}>
                                        {roleLabels[role] ?? role}
                                    </SelectItem>
                                ))}
                            </SelectContent>
                        </Select>
                        <InputError message={errors.role} />
                    </div>
                </div>
            </form>
        </>
    );
}
