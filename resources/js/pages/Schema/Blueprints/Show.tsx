import React, { useState } from 'react';
import { Head, useForm, router } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Breadcrumbs } from '@/components/breadcrumbs';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Checkbox } from '@/components/ui/checkbox';

export default function BlueprintShow({ blueprint }: { blueprint: any }) {
    const [isCreateOpen, setIsCreateOpen] = useState(false);
    
    const { data, setData, post, processing, errors, reset } = useForm({
        name: '',
        handle: '',
        type: 'text',
        is_translatable: false,
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post(`/admin/schema/blueprints/${blueprint.id}/fields`, {
            onSuccess: () => {
                setIsCreateOpen(false);
                reset();
            },
        });
    };

    const deleteField = (id: string) => {
        if (confirm('Are you sure you want to remove this field?')) {
            router.delete(`/admin/schema/fields/${id}`);
        }
    };

    const breadcrumbs = [
        { title: 'Schema Builder', href: '/admin/schema/collections' },
        { title: blueprint.collection?.name || 'Collection', href: `/admin/schema/collections/${blueprint.collection_id}` },
        { title: blueprint.name, href: `/admin/schema/blueprints/${blueprint.id}` },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`${blueprint.name} | Schema Builder`} />
            <div className="flex flex-col gap-6 p-6">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-bold tracking-tight">{blueprint.name}</h1>
                        <p className="text-muted-foreground">Define the data structure for this blueprint.</p>
                    </div>
                    <Dialog open={isCreateOpen} onOpenChange={setIsCreateOpen}>
                        <DialogTrigger asChild>
                            <Button>Add Field</Button>
                        </DialogTrigger>
                        <DialogContent>
                            <DialogHeader>
                                <DialogTitle>Add New Field</DialogTitle>
                            </DialogHeader>
                            <form onSubmit={submit} className="space-y-4">
                                <div className="space-y-2">
                                    <Label htmlFor="name">Field Name</Label>
                                    <Input
                                        id="name"
                                        value={data.name}
                                        onChange={(e) => setData('name', e.target.value)}
                                        placeholder="e.g. Title"
                                    />
                                    {errors.name && <p className="text-sm text-red-500">{errors.name}</p>}
                                </div>
                                <div className="space-y-2">
                                    <Label htmlFor="handle">Handle (JSON Key)</Label>
                                    <Input
                                        id="handle"
                                        value={data.handle}
                                        onChange={(e) => setData('handle', e.target.value)}
                                        placeholder="e.g. title"
                                    />
                                    {errors.handle && <p className="text-sm text-red-500">{errors.handle}</p>}
                                </div>
                                <div className="space-y-2">
                                    <Label htmlFor="type">Field Type</Label>
                                    <Select value={data.type} onValueChange={(val) => setData('type', val)}>
                                        <SelectTrigger>
                                            <SelectValue placeholder="Select type" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="text">Text (Single Line)</SelectItem>
                                            <SelectItem value="textarea">Textarea (Multi Line)</SelectItem>
                                            <SelectItem value="number">Number</SelectItem>
                                            <SelectItem value="boolean">Boolean (Toggle)</SelectItem>
                                            <SelectItem value="media">Media (Image/File)</SelectItem>
                                            <SelectItem value="relation">Relation</SelectItem>
                                        </SelectContent>
                                    </Select>
                                    {errors.type && <p className="text-sm text-red-500">{errors.type}</p>}
                                </div>
                                <div className="flex items-center space-x-2 py-2">
                                    <Checkbox 
                                        id="is_translatable" 
                                        checked={data.is_translatable}
                                        onCheckedChange={(checked) => setData('is_translatable', checked as boolean)}
                                    />
                                    <Label htmlFor="is_translatable" className="font-normal cursor-pointer">
                                        This field is translatable (multi-language)
                                    </Label>
                                </div>
                                <div className="flex justify-end gap-2">
                                    <Button type="button" variant="outline" onClick={() => setIsCreateOpen(false)}>
                                        Cancel
                                    </Button>
                                    <Button type="submit" disabled={processing}>
                                        Save Field
                                    </Button>
                                </div>
                            </form>
                        </DialogContent>
                    </Dialog>
                </div>

                <div className="rounded-md border">
                    <div className="relative w-full overflow-auto">
                        <table className="w-full caption-bottom text-sm">
                            <thead className="[&_tr]:border-b">
                                <tr className="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                                    <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground w-12">#</th>
                                    <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Name</th>
                                    <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Handle</th>
                                    <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Type</th>
                                    <th className="h-12 px-4 text-center align-middle font-medium text-muted-foreground">Translatable</th>
                                    <th className="h-12 px-4 text-right align-middle font-medium text-muted-foreground">Actions</th>
                                </tr>
                            </thead>
                            <tbody className="[&_tr:last-child]:border-0">
                                {!blueprint.fields || blueprint.fields.length === 0 ? (
                                    <tr>
                                        <td colSpan={6} className="p-4 text-center text-muted-foreground">
                                            No fields defined yet. Add a field to build the schema.
                                        </td>
                                    </tr>
                                ) : (
                                    blueprint.fields.map((field: any, index: number) => (
                                        <tr key={field.id} className="border-b transition-colors hover:bg-muted/50">
                                            <td className="p-4 align-middle text-muted-foreground">{index + 1}</td>
                                            <td className="p-4 align-middle font-medium">{field.name}</td>
                                            <td className="p-4 align-middle"><code>{field.handle}</code></td>
                                            <td className="p-4 align-middle"><span className="inline-flex items-center rounded-md border px-2.5 py-0.5 text-xs font-semibold">{field.type}</span></td>
                                            <td className="p-4 align-middle text-center">{field.is_translatable ? '✅' : '-'}</td>
                                            <td className="p-4 align-middle text-right">
                                                <Button variant="destructive" size="sm" onClick={() => deleteField(field.id)}>
                                                    Remove
                                                </Button>
                                            </td>
                                        </tr>
                                    ))
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
