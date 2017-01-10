<?php

/*
 * This file is part of Jitamin.
 *
 * Copyright (C) Jitamin Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jitamin\Model;

use Jitamin\Foundation\Database\Model;
use PDO;

/**
 * Link model.
 */
class LinkModel extends Model
{
    /**
     * SQL table name.
     *
     * @var string
     */
    const TABLE = 'links';

    /**
     * Get a link by id.
     *
     * @param int $link_id Link id
     *
     * @return array
     */
    public function getById($link_id)
    {
        return $this->db->table(self::TABLE)->eq('id', $link_id)->findOne();
    }

    /**
     * Get a link by name.
     *
     * @param string $label
     *
     * @return array
     */
    public function getByLabel($label)
    {
        return $this->db->table(self::TABLE)->eq('label', $label)->findOne();
    }

    /**
     * Get the opposite link id.
     *
     * @param int $link_id Link id
     *
     * @return int
     */
    public function getOppositeLinkId($link_id)
    {
        return $this->db->table(self::TABLE)->eq('id', $link_id)->findOneColumn('opposite_id') ?: $link_id;
    }

    /**
     * Get all links.
     *
     * @return array
     */
    public function getAll()
    {
        return $this->db->table(self::TABLE)->findAll();
    }

    /**
     * Get merged links.
     *
     * @return array
     */
    public function getMergedList()
    {
        return $this->db
                    ->execute('
                        SELECT
                            links.id, links.label, opposite.label as opposite_label
                        FROM links
                        LEFT JOIN links AS opposite ON opposite.id=links.opposite_id
                    ')
                    ->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get label list.
     *
     * @param int  $exclude_id Exclude this link
     * @param bool $prepend    Prepend default value
     *
     * @return array
     */
    public function getList($exclude_id = 0, $prepend = true)
    {
        $labels = $this->db->hashtable(self::TABLE)->neq('id', $exclude_id)->asc('id')->getAll('id', 'label');

        foreach ($labels as &$value) {
            $value = t($value);
        }

        return $prepend ? [''] + $labels : $labels;
    }

    /**
     * Create a new link label.
     *
     * @param string $label
     * @param string $opposite_label
     *
     * @return bool|int
     */
    public function create($label, $opposite_label = '')
    {
        $this->db->startTransaction();

        if (!$this->db->table(self::TABLE)->insert(['label' => $label])) {
            $this->db->cancelTransaction();

            return false;
        }

        $label_id = $this->db->getLastId();

        if (!empty($opposite_label)) {
            $this->db
                ->table(self::TABLE)
                ->insert([
                    'label'       => $opposite_label,
                    'opposite_id' => $label_id,
                ]);

            $this->db
                ->table(self::TABLE)
                ->eq('id', $label_id)
                ->update([
                    'opposite_id' => $this->db->getLastId(),
                ]);
        }

        $this->db->closeTransaction();

        return (int) $label_id;
    }

    /**
     * Update a link.
     *
     * @param array $values
     *
     * @return bool
     */
    public function update(array $values)
    {
        return $this->db
                    ->table(self::TABLE)
                    ->eq('id', $values['id'])
                    ->update([
                        'label'       => $values['label'],
                        'opposite_id' => $values['opposite_id'],
                    ]);
    }

    /**
     * Remove a link a the relation to its opposite.
     *
     * @param int $link_id
     *
     * @return bool
     */
    public function remove($link_id)
    {
        $this->db->table(self::TABLE)->eq('opposite_id', $link_id)->update(['opposite_id' => 0]);

        return $this->db->table(self::TABLE)->eq('id', $link_id)->remove();
    }
}
