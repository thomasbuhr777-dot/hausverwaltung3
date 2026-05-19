<?php

namespace App\Models;

use CodeIgniter\Model;

class EinheitModel extends Model
{
    protected $table            = 'einheiten';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;

    protected $allowedFields = [
        'objekt_id',
        'einheitenart_id',
        'einheitenlage_id',
        'bezeichnung',
        'etage',
        'flaeche',
        'zimmer',
        'beschreibung',
        'status',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules = [
        'objekt_id'            => 'required|integer|is_not_unique[objekte.id]',
        'einheitenart_id'      => 'required|integer|is_not_unique[einheitenarten.id]',
        'einheitenlage_id'     => 'required|integer',
        'bezeichnung'          => 'required|min_length[1]|max_length[100]',
        'flaeche'              => 'permit_empty|decimal',
        'zimmer'               => 'permit_empty|decimal',
        'status'               => 'permit_empty|in_list[verfuegbar,vermietet,gesperrt]',
    ];

    public function getEinheitenMitDetails(?int $objektId = null): array
    {
        $builder = $this->db->table('einheiten e')
            ->select('
                e.*,
                o.bezeichnung AS objekt_bezeichnung,
                o.strasse,
                o.ort,

                ea.bezeichnung AS einheitenart_bezeichnung,
                lg.bezeichnung AS lage_bezeichnung,

                mv.id AS mietvertrag_id,
                mv.mieter_name,
                mv.kaltmiete,
                mv.nebenkosten,
                mv.beginn_datum
            ')
            ->join('objekte o', 'o.id = e.objekt_id', 'left')
            ->join('einheitenarten ea', 'ea.id = e.einheitenart_id', 'left')
            ->join('einheitenlage lg', 'lg.id = e.einheitenlage_id', 'left')
            ->join('mietvertraege mv', 'mv.einheit_id = e.id AND mv.status = "aktiv" AND mv.deleted_at IS NULL', 'left')
            ->where('e.deleted_at IS NULL')
            ->where('o.deleted_at IS NULL');

        if ($objektId !== null) {
            $builder->where('e.objekt_id', $objektId);
        }

        return $builder
            ->orderBy('o.bezeichnung', 'ASC')
            ->orderBy('e.bezeichnung', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function getVerfuegbareEinheiten(): array
    {
        return $this->getEinheitenWithArtBuilder()
            ->where('e.status', 'verfuegbar')
            ->orderBy('e.bezeichnung', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function findAllWithArt(): array
    {
        return $this->getEinheitenWithArtBuilder()
            ->orderBy('e.bezeichnung', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function setVermietet(int $id): bool
    {
        return $this->update($id, ['status' => 'vermietet']);
    }

    private function getEinheitenWithArtBuilder(): \CodeIgniter\Database\BaseBuilder
    {
        return $this->db->table('einheiten e')
            ->select('e.*, ea.bezeichnung AS einheitenart_bezeichnung')
            ->join('objekte o', 'o.id = e.objekt_id AND o.deleted_at IS NULL', 'inner')
            ->join('einheitenarten ea', 'ea.id = e.einheitenart_id', 'left')
            ->where('e.deleted_at IS NULL');
    }

}
